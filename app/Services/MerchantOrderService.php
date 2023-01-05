<?php

namespace App\Services;

use App\Models\MerchantOrder;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MerchantOrderService extends BaseService
{
    public function createMerchantOrder(int $userId, int $merchantId, string $paymentAmount)
    {
        $order = MerchantOrder::new();
        $order->order_sn = $this->generateOrderSn();
        $order->user_id = $userId;
        $order->merchant_id = $merchantId;
        $order->payment_amount = $paymentAmount;
        $order->save();
        return $order;
    }

    public function generateOrderSn()
    {
        return retry(5, function () {
            $orderSn = date('YmdHis') . rand(100000,999999);
            if ($this->isOrderSnExists($orderSn)) {
                Log::warning('当前订单号已存在，orderSn：' . $orderSn);
                $this->throwBusinessException(CodeResponse::FAIL, '订单号生成失败');
            }
            return $orderSn;
        });
    }

    public function isOrderSnExists(string $orderSn)
    {
        return MerchantOrder::query()->where('order_sn', $orderSn)->exists();
    }

    public function getWxPayOrder(int $userId, int $orderId, int $openid)
    {
        $order = MerchantOrder::query()->where('user_id', $userId)->find($orderId);

        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if ($order->status != 0) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单已支付，请勿重复操作');
        }

        return [
            'out_trade_no' => $order->order_sn,
            'body' => '商家订单：' . $order->order_sn,
            'total_fee' => bcmul($order->payment_amount, 100),
            'openid' => $openid
        ];
    }

    public function getOrderList(PageInput $input, $columns = ['*'])
    {
        return MerchantOrder::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }
}
