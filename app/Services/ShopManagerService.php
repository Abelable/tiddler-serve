<?php

namespace App\Services;

use App\Models\ShopManager;

class ShopManagerService extends BaseService
{
    public function createManager($shopId, $userId, $roleId)
    {
        $manager = ShopManager::new();
        $manager->shop_id = $shopId;
        $manager->user_id = $userId;
        $manager->role_id = $roleId;
        $manager->save();
        return $manager;
    }

    public function getManagerList($shopId, $columns = ['*'])
    {
        return ShopManager::query()->where('shop_id', $shopId)->get($columns);
    }

    public function deleteManager($shopId, $userId)
    {
        ShopManager::query()->where('shop_id', $shopId)->where('user_id', $userId)->delete();
    }
}
