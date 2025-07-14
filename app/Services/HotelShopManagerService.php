<?php

namespace App\Services;

use App\Models\HotelShopManager;

class HotelShopManagerService extends BaseService
{
    public function createManager($shopId, $userId, $roleId)
    {
        $manager = HotelShopManager::new();
        $manager->shop_id = $shopId;
        $manager->user_id = $userId;

        return $this->updateManager($manager, $roleId);
    }

    public function updateManager(HotelShopManager $manager, $roleId)
    {
        $manager->role_id = $roleId;
        $manager->save();

        return $manager;
    }

    public function getManagerList($shopId, $columns = ['*'])
    {
        return HotelShopManager::query()->where('shop_id', $shopId)->get($columns);
    }

    public function getListByIds(array $ids, $columns = ['*'])
    {
        return HotelShopManager::query()->whereIn('id', $ids)->get($columns);
    }

    public function getShopManager($shopId, $id, $columns = ['*'])
    {
        return HotelShopManager::query()->where('shop_id', $shopId)->find($id, $columns);
    }
}
