<?php

namespace App\Services;

use App\Models\Catering\SetMealOrder;
use App\Models\User;
use App\Utils\CodeResponse;
use App\Utils\Enums\AccountChangeType;
use App\Utils\Enums\ProductType;
use App\Utils\Enums\SetMealOrderStatus;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\SetMealOrderInput;
use App\Utils\WxMpServe;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yansongda\LaravelPay\Facades\Pay;
use Yansongda\Pay\Exceptions\GatewayException;

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

    public function getShopDateQuery($shopId, $dateDesc = 'today')
    {
        switch ($dateDesc) {
            case 'today':
                $date = Carbon::today();
                break;
            case 'yesterday':
                $date = Carbon::yesterday();
                break;
        }

        return SetMealOrder::query()
            ->where('shop_id', $shopId)
            ->whereDate('created_at', $date)
            ->whereIn('status', [201, 301, 401, 402, 403, 501, 502]);
    }

    public function getUserOrderById($userId, $id, $columns = ['*'])
    {
        return SetMealOrder::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getUserOrderList($userId, $ids, $columns = ['*'])
    {
        return SetMealOrder::query()
            ->where('user_id', $userId)
            ->whereIn('id', $ids)
            ->get($columns);
    }

    public function getOrderListByIds(array $ids, $columns = ['*'])
    {
        return SetMealOrder::query()->whereIn('id', $ids)->get($columns);
    }

    public function getUnpaidOrder(int $userId, $orderId, $columns = ['*'])
    {
        return SetMealOrder::query()
            ->where('user_id', $userId)
            ->where('id', $orderId)
            ->where('status', SetMealOrderStatus::CREATED)
            ->first($columns);
    }

    public function getUnpaidOrderBySn($orderSn, $columns = ['*'])
    {
        return SetMealOrder::query()
            ->where('order_sn', $orderSn)
            ->where('status', SetMealOrderStatus::CREATED)
            ->first($columns);
    }

    public function getPaidOrderById($id, $columns = ['*'])
    {
        return SetMealOrder::query()
            ->where('status', SetMealOrderStatus::PAID)
            ->find($id, $columns);
    }

    // todo 核销有效期
    public function getTimeoutUnConfirmOrders($columns = ['*'])
    {
        return SetMealOrder::query()
            ->where('status', SetMealOrderStatus::PAID)
            ->where('pay_time', '<=', now()->subDays(30))
            ->where('pay_time', '>', now()->subDays(45))
            ->get($columns);
    }

    public function getTimeoutUnFinishedOrders($columns = ['*'])
    {
        return SetMealOrder::query()
            ->whereIn('status', [
                SetMealOrderStatus::CONFIRMED,
                SetMealOrderStatus::AUTO_CONFIRMED,
                SetMealOrderStatus::ADMIN_CONFIRMED
            ])
            ->where('confirm_time', '<=', now()->subDays(15))
            ->where('confirm_time', '>', now()->subDays(30))
            ->get($columns);
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
            AccountService::getInstance()->updateBalance(
                $user->id,
                AccountChangeType::PURCHASE,
                -$deductionBalance,
                $orderSn,
                ProductType::SET_MEAL
            );
        }

        $order = SetMealOrder::new();
        $order->order_sn = $orderSn;
        $order->status = SetMealOrderStatus::CREATED;
        $order->user_id = $user->id;
        $order->consignee = $user->nickname;
        $order->mobile = $user->mobile;
        $order->deduction_balance = $deductionBalance;
        $order->payment_amount = $paymentAmount;
        $order->refund_amount = $paymentAmount;
        $order->save();

        // 设置订单支付超时任务
        // dispatch(new OverTimeCancelOrder($userId, $order->id));

        return $order;
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
            'attach' => $order->order_sn,
            'total_fee' => bcmul($order->payment_amount, 100),
            'openid' => $openid
        ];
    }

    public function wxPaySuccess(array $data)
    {
        $orderSn = $data['attach'];
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
        $order->pay_time = now()->format('Y-m-d\TH:i:s');
        $order->status = SetMealOrderStatus::PAID;
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }

        // 同步微信后台订单发货
        $openid = UserService::getInstance()->getUserById($order->user_id)->openid;
        WxMpServe::new()->verify($openid, $order->pay_id);

        // 佣金记录状态更新为：已支付待结算
        CommissionService::getInstance()
            ->updateListToOrderPaidStatus([$order->id], ProductType::SET_MEAL);

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
        if ($order->status != SetMealOrderStatus::CREATED) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能取消');
        }
        switch ($role) {
            case 'system':
                $order->status = SetMealOrderStatus::AUTO_CANCELED;
                break;
            case 'admin':
                $order->status = SetMealOrderStatus::ADMIN_CANCELED;
                break;
            case 'user':
                $order->status = SetMealOrderStatus::CANCELED;
                break;
        }
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }

        // 删除佣金记录
        CommissionService::getInstance()
            ->deleteUnpaidListByOrderIds([$order->id], ProductType::SET_MEAL);

        return $order;
    }

    public function userConfirm($userId, $orderId)
    {
        $orderList = $this->getUserOrderList($userId, [$orderId]);
        if (count($orderList) == 0) {
            $this->throwBadArgumentValue();
        }
        return $this->confirm($orderList);
    }

    public function adminConfirm($orderIds)
    {
        return DB::transaction(function () use ($orderIds) {
            $orderList = $this->getOrderListByIds($orderIds);
            if (count($orderList) == 0) {
                $this->throwBadArgumentValue();
            }
            return $this->confirm($orderList, 'admin');
        });
    }

    public function systemConfirm()
    {
        return DB::transaction(function () {
            $orderList = $this->getTimeoutUnConfirmOrders();
            if (count($orderList) != 0) {
                $this->confirm($orderList, 'system');
            }
        });
    }

    public function confirm($orderList, $role = 'user')
    {
        $orderList = $orderList->map(function (SetMealOrder $order) use ($role) {
            if (!$order->canConfirmHandle()) {
                $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单无法确认');
            }
            switch ($role) {
                case 'system':
                    $order->status = SetMealOrderStatus::AUTO_CONFIRMED;
                    break;
                case 'admin':
                    $order->status = SetMealOrderStatus::ADMIN_CONFIRMED;

                    break;
                case 'user':
                    $order->status = SetMealOrderStatus::CONFIRMED;
                    break;
            }
            $order->confirm_time = now()->format('Y-m-d\TH:i:s');
            if ($order->cas() == 0) {
                $this->throwUpdateFail();
            }

            return $order;
        });

        // 佣金记录变更为待提现
        $orderIds = $orderList->pluck('id')->toArray();
        CommissionService::getInstance()
            ->updateListToOrderConfirmStatus($orderIds, ProductType::SET_MEAL, $role);

        // todo 设置7天之后打款商家的定时任务，并通知管理员及商家。中间有退货的，取消定时任务。

        return $orderList;
    }

    public function systemFinish()
    {
        $orderList = $this->getTimeoutUnFinishedOrders();
        if (count($orderList) != 0) {
            $orderList->map(function (SetMealOrder $order) {
                if (!$order->canFinishHandle()) {
                    $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能设置为完成状态');
                }
                $order->status = SetMealOrderStatus::AUTO_FINISHED;
                if ($order->cas() == 0) {
                    $this->throwUpdateFail();
                }
            });

            // todo 酒店默认好评
        }
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
        $order->status = SetMealOrderStatus::FINISHED;
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }
        return $order;
    }

    public function userRefund($userId, $orderId)
    {
        $order = $this->getUserOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        $this->refund($order);
    }

    public function adminRefund($orderIds)
    {
        $orderList = $this->getOrderListByIds($orderIds);
        if (count($orderList) == 0) {
            $this->throwBadArgumentValue();
        }
        foreach ($orderList as $order) {
            $this->refund($order);
        }
    }

    public function refund(SetMealOrder $order)
    {
        if (!$order->canRefundHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '该订单不能申请退款');
        }
        DB::transaction(function () use ($order) {
            try {
                // 微信退款
                if ($order->refund_amount != 0) {
                    $refundParams = [
                        'transaction_id' => $order->pay_id,
                        'out_refund_no' => time(),
                        'total_fee' => bcmul($order->payment_amount, 100),
                        'refund_fee' => bcmul($order->refund_amount, 100),
                        'refund_desc' => '餐饮套餐退款',
                        'type' => 'miniapp'
                    ];

                    $result = Pay::wechat()->refund($refundParams);
                    $order->refund_id = $result['refund_id'];
                    Log::info('set_meal_wx_refund', $result->toArray());
                }

                $order->status = SetMealOrderStatus::REFUNDED;
                $order->refund_time = now()->format('Y-m-d\TH:i:s');
                if ($order->cas() == 0) {
                    $this->throwUpdateFail();
                }

                // 退还余额
                if ($order->deduction_balance != 0) {
                    AccountService::getInstance()->updateBalance(
                        $order->user_id,
                        AccountChangeType::REFUND,
                        $order->deduction_balance,
                        $order->order_sn,
                        ProductType::SET_MEAL
                    );
                }

                // 删除佣金记录
                CommissionService::getInstance()
                    ->deletePaidListByOrderIds([$order->id], ProductType::SET_MEAL);

                // todo 通知商家
            } catch (GatewayException $exception) {
                Log::error('wx_refund_fail', [$exception]);
            }
        });
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
}
