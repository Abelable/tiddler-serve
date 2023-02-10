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

    public function getExistCart($goodsId, $selectedSkuIndex, $id = 0, $columns = ['*'])
    {
        $query = Cart::query();
        if ($id != 0) {
            $query = $query->where('id', '!=', $id);
        }
        return $query
                ->where('goods_id', $goodsId)
                ->where('selected_sku_index', $selectedSkuIndex)
                ->first($columns);
    }

    public function getCartById($id, $columns = ['*'])
    {
        return Cart::query()->find($id, $columns);
    }

    public function deleteCartList($userId, array $ids)
    {
        return Cart::query()->where('user_id', $userId)->whereIn('id', $ids)->delete();
    }
}
