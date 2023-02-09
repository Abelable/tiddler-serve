<?php

namespace App\Services;

use App\Models\Cart;

class CartService extends BaseService
{
    public function cartGoodsNumber($userId)
    {
        return Cart::query()->where('user_id', $userId)->sum('number');
    }
}
