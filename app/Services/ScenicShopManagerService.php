<?php

namespace App\Services;

use App\Models\ScenicShopManager;

class ScenicShopManagerService extends BaseService
{
    public function createManager($shopId, $userId, $roleId)
    {
        $manager = ScenicShopManager::new();
        $manager->shop_id = $shopId;
        $manager->user_id = $userId;

        return $this->updateManager($manager, $userId, $roleId);
    }

    public function updateManager(ScenicShopManager $manager, $roleId)
    {
        $manager->role_id = $roleId;
        $manager->save();

        return $manager;
    }

    public function getManagerList($shopId, $columns = ['*'])
    {
        return ScenicShopManager::query()->where('shop_id', $shopId)->get($columns);
    }

    public function getListByIds(array $ids, $columns = ['*'])
    {
        return ScenicShopManager::query()->whereIn('id', $ids)->get($columns);
    }

    public function getShopManager($id, $columns = ['*'])
    {
        return ScenicShopManager::query()->find($id, $columns);
    }

    public function deleteManager($shopId, $userId)
    {
        ScenicShopManager::query()->where('shop_id', $shopId)->where('user_id', $userId)->delete();
    }
}
