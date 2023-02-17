<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\OrderGoods;

class OrderGoodsService extends BaseService
{
    public function createList(array $cartList, $orderId)
    {
        /** @var Cart $cart */
        foreach ($cartList as $cart) {
            $goods = OrderGoods::new();
            $goods->order_id = $orderId;
            $goods->goods_id = $cart->goods_id;
            $goods->image = $cart->goods_image;
            $goods->name = $cart->goods_name;
            $goods->selected_sku_name = $cart->selected_sku_name;
            $goods->selected_sku_index = $cart->selected_sku_index;
            $goods->price = $cart->price;
            $goods->number = $cart->number;
            $goods->save();
        }
    }

    public function getListByOrderId($orderId, $columns = ['*'])
    {
        return OrderGoods::query()->where('order_id', $orderId)->get($columns);
    }


}
