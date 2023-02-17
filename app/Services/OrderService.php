<?php

namespace App\Services;

use App\Jobs\OverTimeCancelOrder;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Shop;
use App\Utils\CodeResponse;
use App\Utils\Enums\OrderEnums;
use Illuminate\Support\Facades\Log;

class OrderService extends BaseService
{
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
        return Order::query()->where('order_sn', $orderSn)->exists();
    }

    public function createOrder($userId, $cartList, Address $address, Shop $shopInfo = null)
    {
        $goodsPrice = 0;
        $freightPrice = 0;

        $goodsList = $cartList->map(function (Cart $cart) use (&$goodsPrice) {
            $price = bcmul($cart->price, $cart->number, 2);
            $goodsPrice = bcadd($goodsPrice, $price, 2);
            // todo 计算运费

            // 商品减库存
            GoodsService::getInstance()->reduceStock($cart->goods_id, $cart->number, $cart->selected_sku_index);

            return [
                'id' => $cart->goods_id,
                'image' => $cart->goods_image,
                'name' => $cart->goods_name,
                'selected_sku_name' => $cart->selected_sku_name,
                'selected_sku_index' => $cart->selected_sku_index,
                'price' => $cart->price,
                'number' => $cart->number,
            ];
        });

        $order = Order::new();
        $order->order_sn = OrderService::getInstance()->generateOrderSn();
        $order->status = OrderEnums::STATUS_CREATE;
        $order->user_id = $userId;
        $order->consignee = $address->name;
        $order->mobile = $address->mobile;
        $order->address = $address->region_desc . ' ' . $address->address_detail;
        if (!is_null($shopInfo)) {
            $order->shop_id = $shopInfo->id;
            $order->shop_avatar = $shopInfo->avatar;
            $order->shop_name = $shopInfo->name;
        } else {
            $order->shop_name = '官方自营';
        }
        $order->goods_list = json_encode($goodsList);
        $order->goods_price = $goodsPrice;
        $order->freight_price = $freightPrice;
        $order->payment_amount = bcadd($goodsPrice, $freightPrice, 2);
        $order->refund_amount = $order->payment_amount;
        $order->save();

        // 设置订单支付超时任务
        dispatch(new OverTimeCancelOrder($userId, $order->id));

        return $order->id;
    }

    public function getUnpaidList(int $userId, array $orderIds, $columns = ['*'])
    {
        return Order::query()
            ->where('user_id', $userId)
            ->whereIn('id', $orderIds)
            ->where('status', OrderEnums::STATUS_CREATE)
            ->get($columns);
    }

    public function getUnpaidListBySn(array $orderSnList, $columns = ['*'])
    {
        return Order::query()
            ->whereIn('order_sn', $orderSnList)
            ->where('status', OrderEnums::STATUS_CREATE)
            ->get($columns);
    }

    public function createWxPayOrder(int $userId, array $orderIds, int $openid)
    {
        $orderList = $this->getUnpaidList($userId, $orderIds);
        if (count($orderList) == 0) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '订单不存在');
        }

        $orderSnList = $orderList->pluck('order_sn')->toArray();

        $paymentAmount = 0;
        foreach ($orderList as $order) {
            $paymentAmount = bcadd($order->payment_amount, $paymentAmount, 2);
        }

        return [
            'out_trade_no' => time(),
            'body' => 'order_sn_list:' . json_encode($orderSnList),
            'total_fee' => bcmul($paymentAmount, 100),
            'openid' => $openid
        ];
    }

    public function wxPaySuccess(array $data)
    {
        $orderSnList = $data['body'] ?
            json_encode(str_replace('order_sn_list:', '', $data['body'])) : [];
        $payId = $data['transaction_id'] ?? '';
        $actualPaymentAmount = $data['total_fee'] ? bcdiv($data['total_fee'], 100, 2) : 0;

        $orderList = $this->getUnpaidListBySn($orderSnList);

        $paymentAmount = 0;
        foreach ($orderList as $order) {
            $paymentAmount = bcadd($order->payment_amount, $paymentAmount, 2);
        }
        if (bccomp($actualPaymentAmount, $paymentAmount, 2) != 0) {
            $errMsg = "支付回调，订单{$data['body']}金额不一致，请检查，支付回调金额：{$actualPaymentAmount}，订单总金额：{$paymentAmount}";
            Log::error($errMsg);
            $this->throwBusinessException(CodeResponse::FAIL, $errMsg);
        }

        $orderList = $orderList->map(function (Order $order) use ($payId) {
            $order->pay_id = $payId;
            $order->pay_time = now()->toDateTimeString();
            $order->status = OrderEnums::STATUS_PAY;
            if ($order->cas() == 0) {
                $this->throwUpdateFail();
            }
            // todo 通知（邮件或钉钉）管理员、
            // todo 通知（短信、系统消息）商家
            return $order;
        });

        return $orderList;
    }

    public function SystemCancel($userId, $orderId)
    {

    }
}
