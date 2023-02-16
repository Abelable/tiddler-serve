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
        return Order::query()->where('order_sn', $orderSn)->exists();
    }

    public function createOrder($userId, $cartList,  Address $address, Shop $shopInfo = null)
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

    public function SystemCancel($userId, $orderId)
    {

    }
}
