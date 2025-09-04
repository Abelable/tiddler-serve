<?php

namespace App\Services\Mall\Catering;

use App\Models\Catering\CateringShopManager;
use App\Services\BaseService;

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

    public function getManagerByUserId($shopId, $userId, $columns = ['*'])
    {
        return CateringShopManager::query()
            ->where('shop_id', $shopId)
            ->where('user_id', $userId)
            ->first($columns);
    }
}
