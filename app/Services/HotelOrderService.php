<?php

namespace App\Services;

use App\Models\HotelOrder;
use App\Models\HotelShop;
use App\Utils\CodeResponse;
use App\Utils\Enums\AccountChangeType;
use App\Utils\Enums\HotelOrderStatus;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\HotelOrderInput;
use App\Utils\Inputs\PageInput;
use App\Utils\WxMpServe;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yansongda\LaravelPay\Facades\Pay;
use Yansongda\Pay\Exceptions\GatewayException;

class HotelOrderService extends BaseService
{
    public function getTotal($userId, $statusList)
    {
        return HotelOrder::query()
            ->where('user_id', $userId)
            ->whereIn('status', $statusList)
            ->count();
    }

    public function getShopTotal($shopId, $statusList)
    {
        return HotelOrder::query()
            ->where('shop_id', $shopId)
            ->whereIn('status', $statusList)
            ->count();
    }

    public function getOrderListByStatus($userId, $statusList, PageInput $input, $columns = ['*'])
    {
        $query = HotelOrder::query()->where('user_id', $userId);
        if (count($statusList) != 0) {
            $query = $query->whereIn('status', $statusList);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getShopOrderList($shopId, $statusList, PageInput $input, $columns = ['*'])
    {
        $query = HotelOrder::query()->where('shop_id', $shopId);
        if (count($statusList) != 0) {
            $query = $query->whereIn('status', $statusList);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getOrderById($userId, $id, $columns = ['*'])
    {
        return HotelOrder::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getUserOrderList($userId, $ids, $columns = ['*'])
    {
        return HotelOrder::query()->where('user_id', $userId)->whereIn('id', $ids)->get($columns);
    }

    public function getUserOrderById($userId, $id, $columns = ['*'])
    {
        return HotelOrder::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getShopOrder($shopId, $id, $columns = ['*'])
    {
        return HotelOrder::query()->where('shop_id', $shopId)->find($id, $columns);
    }

    public function getOrderListByIds(array $ids, $columns = ['*'])
    {
        return HotelOrder::query()->whereIn('id', $ids)->get($columns);
    }

    public function getUnpaidOrder(int $userId, $orderId, $columns = ['*'])
    {
        return HotelOrder::query()
            ->where('user_id', $userId)
            ->where('id', $orderId)
            ->where('status', HotelOrderStatus::CREATED)
            ->first($columns);
    }

    public function getUnpaidOrderBySn($orderSn, $columns = ['*'])
    {
        return HotelOrder::query()
            ->where('order_sn', $orderSn)
            ->where('status', HotelOrderStatus::CREATED)
            ->first($columns);
    }

    public function getApprovedOrderById($id, $columns = ['*'])
    {
        return HotelOrder::query()
            ->where('status', HotelOrderStatus::MERCHANT_APPROVED)
            ->find($id, $columns);
    }

    public function getPendingSettleInOrderById($id, $columns = ['*'])
    {
        return HotelOrder::query()
            ->whereIn('status', [HotelOrderStatus::PAID, HotelOrderStatus::MERCHANT_APPROVED])
            ->find($id, $columns);
    }

    // todo 核销有效期
    public function getTimeoutUnConfirmOrders($columns = ['*'])
    {
        return HotelOrder::query()
            ->where('status', HotelOrderStatus::PAID)
            ->where('pay_time', '<=', now()->subDays(30))
            ->where('pay_time', '>', now()->subDays(45))
            ->get($columns);
    }

    public function getTimeoutUnFinishedOrders($columns = ['*'])
    {
        return HotelOrder::query()
            ->whereIn('status', [
                HotelOrderStatus::CONFIRMED,
                HotelOrderStatus::AUTO_CONFIRMED,
                HotelOrderStatus::ADMIN_CONFIRMED
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
        return HotelOrder::query()->where('order_sn', $orderSn)->exists();
    }

    public function createOrder($userId, HotelOrderInput $input, HotelShop $shop, $paymentAmount)
    {
        $orderSn = $this->generateOrderSn();

        // 余额抵扣
        $deductionBalance = 0;
        if ($input->useBalance == 1) {
            $account = AccountService::getInstance()->getUserAccount($userId);
            $deductionBalance = min($paymentAmount, $account->balance);
            $paymentAmount = bcsub($paymentAmount, $deductionBalance, 2);

            // 更新余额
            AccountService::getInstance()
                ->updateBalance($userId, AccountChangeType::PURCHASE, -$deductionBalance, $orderSn, ProductType::HOTEL);
        }

        $order = HotelOrder::new();
        $order->order_sn = $orderSn;
        $order->status = HotelOrderStatus::CREATED;
        $order->user_id = $userId;
        $order->consignee = $input->consignee;
        $order->mobile = $input->mobile;
        $order->shop_id = $shop->id;
        $order->shop_logo = $shop->logo;
        $order->shop_name = $shop->name;
        $order->deduction_balance = $deductionBalance;
        $order->payment_amount = $paymentAmount;
        $order->total_payment_amount = $paymentAmount;
        $order->refund_amount = $paymentAmount;
        $order->save();

        // 设置订单支付超时任务
        // dispatch(new OverTimeCancelOrder($userId, $order->id));

        return $order;
    }

    public function getDatePriceList($roomId, $checkInDate, $checkOutDate)
    {
        $room = HotelRoomService::getInstance()->getRoomById($roomId, ['price_list']);
        if (is_null($room)) {
            $this->throwBadArgumentValue();
        }
        $priceList = json_decode($room->price_list);

        $dateList = $this->createDateList($checkInDate, $checkOutDate);

        return $dateList->map(function ($date) use ($priceList) {
            $priceUnit = array_filter($priceList, function ($item) use ($date) {
                return $date >= $item->startDate && $date <= $item->endDate;
            })[0];
            return [
                'date' => $date,
                'price' => $priceUnit->price
            ];
        });
    }

    private function createDateList($checkInDate, $checkOutDate)
    {
        $startDate = Carbon::createFromTimestamp($checkInDate);
        $endDate = Carbon::createFromTimestamp($checkOutDate);

        $dateList = collect($startDate->range($endDate))->map(function ($date) {
            return $date->timestamp;
        });
        $dateList->pop();

        return $dateList;
    }

    public function createWxPayOrder($userId, $orderId, $openid)
    {
        /** @var HotelOrder $order */
        $order = $this->getUnpaidOrder($userId, $orderId);
        if (is_null($order)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '订单不存在');
        }

        return [
            'out_trade_no' => time(),
            'body' => 'hotel_order_sn:' . $order->order_sn,
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

        /** @var HotelOrder $order */
        $order = $this->getUnpaidOrderBySn($orderSn);

        if (bccomp($actualPaymentAmount, $order->payment_amount, 2) != 0) {
            $errMsg = "支付回调，订单{$data['body']}金额不一致，请检查，支付回调金额：{$actualPaymentAmount}，订单总金额：{$order->payment_amount}";
            Log::error($errMsg);
            $this->throwBusinessException(CodeResponse::FAIL, $errMsg);
        }

        $order->pay_id = $payId;
        $order->pay_time = now()->format('Y-m-d\TH:i:s');
        $order->status = HotelOrderStatus::PAID;
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }

        // 同步微信后台订单发货
        $openid = UserService::getInstance()->getUserById($order->user_id)->openid;
        WxMpServe::new()->verify($openid, $order->pay_id);

        // 佣金记录状态更新为：已支付待结算
        CommissionService::getInstance()->updateListToOrderPaidStatus([$order->id], ProductType::HOTEL);

        // todo 通知（邮件或钉钉）管理员、
        // todo 通知（短信、系统消息）商家

        return $order;
    }

    public function approve($shopId, $orderId)
    {
        $order = $this->getShopOrder($shopId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canApproveHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单无法确认');
        }

        $order->status = HotelOrderStatus::MERCHANT_APPROVED;
        $order->approve_time = now()->format('Y-m-d\TH:i:s');
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }

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
        if ($order->status != HotelOrderStatus::CREATED) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能取消');
        }
        switch ($role) {
            case 'system':
                $order->status = HotelOrderStatus::AUTO_CANCELED;
                break;
            case 'admin':
                $order->status = HotelOrderStatus::ADMIN_CANCELED;
                break;
            case 'user':
                $order->status = HotelOrderStatus::CANCELED;
                break;
        }
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }

        // 删除佣金记录
        CommissionService::getInstance()->deleteUnpaidListByOrderIds([$order->id], ProductType::HOTEL);

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
        $orderList = $orderList->map(function (HotelOrder $order) use ($role) {
            if (!$order->canConfirmHandle()) {
                $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单无法确认');
            }
            switch ($role) {
                case 'system':
                    $order->status = HotelOrderStatus::AUTO_CONFIRMED;
                    break;
                case 'admin':
                    $order->status = HotelOrderStatus::ADMIN_CONFIRMED;
                    break;
                case 'user':
                    $order->status = HotelOrderStatus::CONFIRMED;
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
        CommissionService::getInstance()->updateListToOrderConfirmStatus($orderIds, ProductType::HOTEL, $role);

        // todo 设置7天之后打款商家的定时任务，并通知管理员及商家。中间有退货的，取消定时任务。

        return $orderList;
    }

    public function systemFinish()
    {
        $orderList = $this->getTimeoutUnFinishedOrders();
        if (count($orderList) != 0) {
            $orderList->map(function (HotelOrder $order) {
                if (!$order->canFinishHandle()) {
                    $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能设置为完成状态');
                }
                $order->status = HotelOrderStatus::AUTO_FINISHED;
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
        $order->status = HotelOrderStatus::FINISHED;
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

    public function shopRefund($shopId, $orderId)
    {
        $order = $this->getShopOrder($shopId, $orderId);
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

    public function refund(HotelOrder $order)
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
                        'total_fee' => bcmul($order->total_payment_amount, 100),
                        'refund_fee' => bcmul($order->refund_amount, 100),
                        'refund_desc' => '酒店房间退款',
                        'type' => 'miniapp'
                    ];

                    $result = Pay::wechat()->refund($refundParams);
                    $order->refund_id = $result['refund_id'];
                    Log::info('hotel_order_wx_refund', $result->toArray());
                }

                $order->status = HotelOrderStatus::REFUNDED;
                $order->refund_time = now()->format('Y-m-d\TH:i:s');
                if ($order->cas() == 0) {
                    $this->throwUpdateFail();
                }

                // 退还余额
                if ($order->deduction_balance != 0) {
                    AccountService::getInstance()
                        ->updateBalance($order->user_id, AccountChangeType::REFUND, $order->deduction_balance, $order->order_sn, ProductType::HOTEL);
                }

                // 删除佣金记录
                CommissionService::getInstance()->deletePaidListByOrderIds([$order->id], ProductType::HOTEL);

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

        OrderGoodsService::getInstance()->delete($order->id);
        $order->delete();
    }
}
