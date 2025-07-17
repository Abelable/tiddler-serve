<?php

namespace App\Services;

use App\Models\CateringShopManager;

class CateringShopManagerService extends BaseService
{
    public function createManager($shopId, $userId, $roleId)
    {
        $manager = CateringShopManager::new();
        $manager->shop_id = $shopId;
        $manager->user_id = $userId;

        return $this->updateManager($manager, $roleId);
    }

    public function updateManager(CateringShopManager $manager, $roleId)
    {
        $manager->role_id = $roleId;
        $manager->save();

        return $manager;
    }

    public function getManagerList($shopId, $columns = ['*'])
    {
        return CateringShopManager::query()->where('shop_id', $shopId)->get($columns);
    }

    public function getListByIds(array $ids, $columns = ['*'])
    {
        return CateringShopManager::query()->whereIn('id', $ids)->get($columns);
    }

    public function getShopManager($shopId, $id, $columns = ['*'])
    {
        return CateringShopManager::query()->where('shop_id', $shopId)->find($id, $columns);
    }
}
