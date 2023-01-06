<?php

namespace App\Services;

use App\Models\Shop;

class ShopService extends BaseService
{
    public function createShop(int $userId, int $merchantId, int $type, string $name, int $categoryId)
    {
        $shop = Shop::new();
        $shop->user_id = $userId;
        $shop->merchant_id = $merchantId;
        $shop->type = $type;
        $shop->name = $name;
        $shop->category_id = $categoryId;
        $shop->save();
        return $shop;
    }
}
