<?php

namespace App\Services;

use App\Models\HotelShopManager;
use App\Utils\Inputs\ManagerInput;

class HotelShopManagerService extends BaseService
{
    public function createManager(ManagerInput $input)
    {
        $manager = HotelShopManager::new();
        $manager->shop_id = $input->shopId;
        $manager->user_id = $input->userId;
        $manager->avatar = $input->avatar;
        $manager->nickname = $input->nickname;

        return $this->updateManager($manager, $input->roleId);
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

    public function getListByShopIds(array $shopIds, $columns = ['*'])
    {
        return HotelShopManager::query()->whereIn('shop_id', $shopIds)->get($columns);
    }

    public function getListByIds(array $ids, $columns = ['*'])
    {
        return HotelShopManager::query()->whereIn('id', $ids)->get($columns);
    }

    public function getShopManager($shopId, $id, $columns = ['*'])
    {
        return HotelShopManager::query()->where('shop_id', $shopId)->find($id, $columns);
    }

    public function getManagerByUserId($shopId, $userId, $columns = ['*'])
    {
        return HotelShopManager::query()
            ->where('shop_id', $shopId)
            ->where('user_id', $userId)
            ->first($columns);
    }
}
