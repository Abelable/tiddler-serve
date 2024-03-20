<?php

namespace App\Services;

use App\Models\MealTicket;
use App\Models\MealTicketOrder;
use App\Models\User;
use App\Utils\CodeResponse;
use App\Utils\Enums\MealTicketOrderEnums;
use App\Utils\Inputs\MealTicketOrderInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MealTicketOrderService extends BaseService
{
    public function getOrderListByStatus($userId, $statusList, PageInput $input, $columns = ['*'])
    {
        $query = MealTicketOrder::query()->where('user_id', $userId);
        if (count($statusList) != 0) {
            $query = $query->whereIn('status', $statusList);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getProviderOrderList($providerId, $statusList, PageInput $input, $columns = ['*'])
    {
        $query = MealTicketOrder::query()->where('provider_id', $providerId);
        if (count($statusList) != 0) {
            $query = $query->whereIn('status', $statusList);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getOrderById($userId, $id, $columns = ['*'])
    {
        return MealTicketOrder::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getUnpaidOrder(int $userId, $orderId, $columns = ['*'])
    {
        return MealTicketOrder::query()
            ->where('user_id', $userId)
            ->where('id', $orderId)
            ->where('status', MealTicketOrderEnums::STATUS_CREATE)
            ->first($columns);
    }

    public function getUnpaidOrderBySn($orderSn, $columns = ['*'])
    {
        return MealTicketOrder::query()
            ->where('order_sn', $orderSn)
            ->where('status', MealTicketOrderEnums::STATUS_CREATE)
            ->first($columns);
    }

    public function generateOrderSn()
    {
        return retry(5, function () {
            $orderSn = date('YmdHis') . rand(100000, 999999);
            if ($this->isOrderSnExists($orderSn)) {
                Log::warning('当前订单号已存在，orderSn：' . $orderSn);
                $this->throwBusinessException(CodeResponse::FAIL, '订单号生成失败');
            }
            return $orderSn;
        });
    }

    public function isOrderSnExists(string $orderSn)
    {
        return MealTicketOrder::query()->where('order_sn', $orderSn)->exists();
    }

    public function createOrder(User $user, MealTicketOrderInput $input)
    {
        /** @var MealTicket $ticket */
        list($paymentAmount, $ticket) = $this->calcPaymentAmount($input->ticketId, $input->num);

        $order = MealTicketOrder::new();
        $order->order_sn = $this->generateOrderSn();
        $order->status = MealTicketOrderEnums::STATUS_CREATE;
        $order->user_id = $user->id;
        $order->consignee = $user->nickname;
        $order->mobile = $user->mobile;
        $order->provider_id = $ticket->provider_id;
        $order->restaurant_id = $input->restaurantId;
        $order->restaurant_name = $input->restaurantName;
        $order->payment_amount = $paymentAmount;
        $order->refund_amount = $paymentAmount;
        $order->save();

        // 生成订单代金券快照
        OrderMealTicketService::getInstance()->createOrderTicket($order->id, $input->num, $ticket);

        // 设置订单支付超时任务
        // dispatch(new OverTimeCancelOrder($userId, $order->id));

        return $order->id;
    }

    public function calcPaymentAmount($ticketId, $num)
    {
        $ticket = MealTicketService::getInstance()->getTicketById($ticketId);
        $paymentAmount = (float)bcmul($ticket->price, $num, 2);
        return [$paymentAmount, $ticket];
    }

    public function createWxPayOrder($userId, $orderId, $openid)
    {
        /** @var MealTicketOrder $order */
        $order = $this->getUnpaidOrder($userId, $orderId);
        if (is_null($order)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '订单不存在');
        }

        return [
            'out_trade_no' => time(),
            'body' => 'meal_ticket_order_sn:' . $order->order_sn,
            'total_fee' => bcmul($order->payment_amount, 100),
            'openid' => $openid
        ];
    }

    public function wxPaySuccess(array $data)
    {
        $orderSn = $data['body'] ? str_replace('meal_ticket_order_sn:', '', $data['body']) : '';
        $payId = $data['transaction_id'] ?? '';
        $actualPaymentAmount = $data['total_fee'] ? bcdiv($data['total_fee'], 100, 2) : 0;

        /** @var MealTicketOrder $order */
        $order = $this->getUnpaidOrderBySn($orderSn);

        if (bccomp($actualPaymentAmount, $order->payment_amount, 2) != 0) {
            $errMsg = "支付回调，订单{$data['body']}金额不一致，请检查，支付回调金额：{$actualPaymentAmount}，订单总金额：{$order->payment_amount}";
            Log::error($errMsg);
            $this->throwBusinessException(CodeResponse::FAIL, $errMsg);
        }

        $order->pay_id = $payId;
        $order->pay_time = now()->toDateTimeString();
        $order->status = MealTicketOrderEnums::STATUS_PAY;
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }
        // todo 通知（邮件或钉钉）管理员、
        // todo 通知（短信、系统消息）商家
        return $order;
    }

    public function userCancel($userId, $orderId)
    {
        return DB::transaction(function () use ($userId, $orderId) {
            return $this->cancel($userId, $orderId);
        });
    }

    public function systemCancel($userId, $orderId)
    {
        return DB::transaction(function () use ($userId, $orderId) {
            return $this->cancel($userId, $orderId, 'system');
        });
    }

    public function cancel($userId, $orderId, $role = 'user')
    {
        $order = $this->getOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if ($order->status != MealTicketOrderEnums::STATUS_CREATE) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能取消');
        }
        switch ($role) {
            case 'system':
                $order->status = MealTicketOrderEnums::STATUS_AUTO_CANCEL;
                break;
            case 'admin':
                $order->status = MealTicketOrderEnums::STATUS_ADMIN_CANCEL;
                break;
            case 'user':
                $order->status = MealTicketOrderEnums::STATUS_CANCEL;
                break;
        }
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }

        return $order;
    }

    public function confirm($userId, $orderId, $isAuto = false)
    {
        $order = $this->getOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }

        $order->status = $isAuto ? MealTicketOrderEnums::STATUS_AUTO_CONFIRM : MealTicketOrderEnums::STATUS_CONFIRM;
        $order->confirm_time = now()->toDateTimeString();
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }

        // todo 设置7天之后打款商家的定时任务，并通知管理员及商家。中间有退货的，取消定时任务。

        return $order;
    }

    public function finish($userId, $orderId)
    {
        $order = $this->getOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canFinishHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能设置为完成状态');
        }
        $order->status = MealTicketOrderEnums::STATUS_FINISHED;
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }
        return $order;
    }

    public function delete($userId, $orderId)
    {
        $order = $this->getOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canDeleteHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能删除');
        }

        OrderMealTicketService::getInstance()->delete($order->id);
        $order->delete();
    }

    public function refund($userId, $orderId)
    {
        $order = $this->getOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canRefundHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '该订单不能申请退款');
        }

        $order->status = MealTicketOrderEnums::STATUS_REFUND;

        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }

        // todo 通知商家
        // todo 开启自动退款定时任务

        return $order;
    }
}
