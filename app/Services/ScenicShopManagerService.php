<?php

namespace App\Services;

use App\Models\ScenicShopManager;
use App\Utils\Inputs\ManagerInput;

class ScenicShopManagerService extends BaseService
{
    public function createManager(ManagerInput $input)
    {
        $manager = ScenicShopManager::new();
        $manager->shop_id = $input->shopId;
        $manager->user_id = $input->userId;
        $manager->avatar = $input->avatar;
        $manager->nickname = $input->nickname;

        return $this->updateManager($manager, $input->roleId);
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

    public function getListByShopIds(array $shopIds, $columns = ['*'])
    {
        return ScenicShopManager::query()->whereIn('shop_id', $shopIds)->get($columns);
    }

    public function getListByIds(array $ids, $columns = ['*'])
    {
        return ScenicShopManager::query()->whereIn('id', $ids)->get($columns);
    }

    public function getShopManager($shopId, $id, $columns = ['*'])
    {
        return ScenicShopManager::query()->where('shop_id', $shopId)->find($id, $columns);
    }

    public function getManagerByUserId($shopId, $userId, $columns = ['*'])
    {
        return ScenicShopManager::query()
            ->where('shop_id', $shopId)
            ->where('user_id', $userId)
            ->first($columns);
    }
}
