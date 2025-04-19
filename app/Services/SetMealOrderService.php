<?php

namespace App\Services;

use App\Models\SetMeal;
use App\Models\SetMealOrder;
use App\Models\User;
use App\Utils\CodeResponse;
use App\Utils\Enums\ProductType;
use App\Utils\Enums\SetMealOrderEnums;
use App\Utils\Inputs\SetMealOrderInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SetMealOrderService extends BaseService
{
    public function getOrderListByStatus($userId, $statusList, PageInput $input, $columns = ['*'])
    {
        $query = SetMealOrder::query()->where('user_id', $userId);
        if (count($statusList) != 0) {
            $query = $query->whereIn('status', $statusList);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getProviderOrderList($providerId, $statusList, PageInput $input, $columns = ['*'])
    {
        $query = SetMealOrder::query()->where('provider_id', $providerId);
        if (count($statusList) != 0) {
            $query = $query->whereIn('status', $statusList);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getOrderById($userId, $id, $columns = ['*'])
    {
        return SetMealOrder::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getUnpaidOrder(int $userId, $orderId, $columns = ['*'])
    {
        return SetMealOrder::query()
            ->where('user_id', $userId)
            ->where('id', $orderId)
            ->where('status', SetMealOrderEnums::STATUS_CREATE)
            ->first($columns);
    }

    public function getUnpaidOrderBySn($orderSn, $columns = ['*'])
    {
        return SetMealOrder::query()
            ->where('order_sn', $orderSn)
            ->where('status', SetMealOrderEnums::STATUS_CREATE)
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
        return SetMealOrder::query()->where('order_sn', $orderSn)->exists();
    }

    public function createOrder(User $user, SetMealOrderInput $input, $providerId, $paymentAmount)
    {
        $orderSn = $this->generateOrderSn();

        // 余额抵扣
        $deductionBalance = 0;
        if ($input->useBalance == 1) {
            $account = AccountService::getInstance()->getUserAccount($user->id);
            $deductionBalance = min($paymentAmount, $account->balance);
            $paymentAmount = bcsub($paymentAmount, $deductionBalance, 2);

            // 更新余额
            AccountService::getInstance()->updateBalance($user->id, 2, -$deductionBalance, $orderSn, ProductType::SET_MEAL);
        }

        $order = SetMealOrder::new();
        $order->order_sn = $orderSn;
        $order->status = SetMealOrderEnums::STATUS_CREATE;
        $order->user_id = $user->id;
        $order->consignee = $user->nickname;
        $order->mobile = $user->mobile;
        $order->provider_id = $providerId;
        $order->restaurant_id = $input->restaurantId;
        $order->restaurant_name = $input->restaurantName;
        $order->deduction_balance = $deductionBalance;
        $order->payment_amount = $paymentAmount;
        $order->total_payment_amount = $paymentAmount;
        $order->refund_amount = $paymentAmount;
        $order->save();

        // 设置订单支付超时任务
        // dispatch(new OverTimeCancelOrder($userId, $order->id));

        return $order;
    }

    public function calcPaymentAmount($setMealId, $num)
    {
        $setMeal = SetMealService::getInstance()->getSetMealById($setMealId);
        $paymentAmount = (float)bcmul($setMeal->price, $num, 2);
        return [$paymentAmount, $setMeal];
    }

    public function createWxPayOrder($userId, $orderId, $openid)
    {
        /** @var SetMealOrder $order */
        $order = $this->getUnpaidOrder($userId, $orderId);
        if (is_null($order)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '订单不存在');
        }

        return [
            'out_trade_no' => time(),
            'body' => 'set_meal_order_sn:' . $order->order_sn,
            'total_fee' => bcmul($order->payment_amount, 100),
            'openid' => $openid
        ];
    }

    public function wxPaySuccess(array $data)
    {
        $orderSn = $data['body'] ? str_replace('set_meal_order_sn:', '', $data['body']) : '';
        $payId = $data['transaction_id'] ?? '';
        $actualPaymentAmount = $data['total_fee'] ? bcdiv($data['total_fee'], 100, 2) : 0;

        /** @var SetMealOrder $order */
        $order = $this->getUnpaidOrderBySn($orderSn);

        if (bccomp($actualPaymentAmount, $order->payment_amount, 2) != 0) {
            $errMsg = "支付回调，订单{$data['body']}金额不一致，请检查，支付回调金额：{$actualPaymentAmount}，订单总金额：{$order->payment_amount}";
            Log::error($errMsg);
            $this->throwBusinessException(CodeResponse::FAIL, $errMsg);
        }

        $order->pay_id = $payId;
        $order->pay_time = now()->toDateTimeString();
        $order->status = SetMealOrderEnums::STATUS_PAY;
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
        if ($order->status != SetMealOrderEnums::STATUS_CREATE) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能取消');
        }
        switch ($role) {
            case 'system':
                $order->status = SetMealOrderEnums::STATUS_AUTO_CANCEL;
                break;
            case 'admin':
                $order->status = SetMealOrderEnums::STATUS_ADMIN_CANCEL;
                break;
            case 'user':
                $order->status = SetMealOrderEnums::STATUS_CANCEL;
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

        $order->status = $isAuto ? SetMealOrderEnums::STATUS_AUTO_CONFIRM : SetMealOrderEnums::STATUS_CONFIRM;
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
        $order->status = SetMealOrderEnums::STATUS_FINISHED;
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

        OrderSetMealService::getInstance()->delete($order->id);
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

        $order->status = SetMealOrderEnums::STATUS_REFUND;

        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }

        // todo 通知商家
        // todo 开启自动退款定时任务

        return $order;
    }
}
