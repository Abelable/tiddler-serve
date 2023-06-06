<?php

namespace App\Services;

use App\Models\MerchantOrder;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\Log;

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

    public function getWxPayOrder(int $userId, int $orderId, string $openid)
    {
        $order = MerchantOrder::query()->where('user_id', $userId)->find($orderId);

        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if ($order->status != 0) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单已支付，请勿重复操作');
        }

        return [
            'out_trade_no' => time(),
            'body' => 'merchant_order_sn:' . $order->order_sn,
            'total_fee' => bcmul($order->payment_amount, 100),
            'openid' => $openid
        ];
    }

    public function wxPaySuccess(array $data)
    {
        $orderSn = $data['body'] ? str_replace('merchant_order_sn:', '', $data['body']) : '';
        $payId = $data['transaction_id'] ?? '';
        $actualPaymentAmount = $data['total_fee'] ? bcdiv($data['total_fee'], 100, 2) : 0;

        $order = $this->getOrderByOrderSn($orderSn);
        if (is_null($order)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '订单不存在');
        }
        if (bccomp($actualPaymentAmount, $order->payment_amount, 2) != 0) {
            $errMsg = "支付回调，订单{$order->id}金额不一致，请检查，支付回调金额：{$actualPaymentAmount}，订单金额：{$order->payment_amount}";
            Log::error($errMsg);
            $this->throwBusinessException(CodeResponse::FAIL, $errMsg);
        }

        $order->pay_id = $payId;
        $order->status = 1;
        $order->save();

        return $order;
    }

    public function getOrderByOrderSn(string $orderSn)
    {
        return MerchantOrder::query()->where('order_sn', $orderSn)->first();
    }

    public function getMerchantOrderByUserId($userId, $columns = ['*'])
    {
        return MerchantOrder::query()->where('user_id', $userId)->first($columns);
    }

    public function getOrderList(PageInput $input, $columns = ['*'])
    {
        return MerchantOrder::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }
}
