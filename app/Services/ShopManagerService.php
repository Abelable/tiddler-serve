<?php

namespace App\Services;

use App\Models\ShopManager;

class ShopManagerService extends BaseService
{
    public function createManager($shopId, $userId)
    {
        $address = ShopManager::new();
        $address->shop_id = $shopId;
        $address->user_id = $userId;
        $address->save();
        return $address;
    }

    public function getManagerList($shopId, $columns = ['*'])
    {
        return ShopManager::query()->where('shop_id', $shopId)->get($columns);
    }

    public function deleteManager($shopId)
    {
        ShopManager::query()->where('shop_id', $shopId)->delete();
    }
}
