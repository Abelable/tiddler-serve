<?php

namespace App\Services;

use App\Models\Cart;

class CartService extends BaseService
{
    public function cartGoodsNumber($userId)
    {
        return Cart::query()->where('user_id', $userId)->sum('number');
    }

    public function cartList($userId, $columns = ['*'])
    {
        return Cart::query()->where('user_id', $userId)->get($columns);
    }

    public function getExistCart($goodsId, $selectedSkuIndex, $columns = ['*'])
    {
        return Cart::query()
                ->where('goods_id', $goodsId)
                ->where('selected_sku_index', $selectedSkuIndex)
                ->first($columns);
    }
    public function getCartById($id, $columns = ['*'])
    {
        return Cart::query()->find($id, $columns);
    }
}
