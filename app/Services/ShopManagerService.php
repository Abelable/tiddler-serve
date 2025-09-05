<?php

namespace App\Services;

use App\Models\ShopManager;
use App\Utils\Inputs\ManagerInput;

class ShopManagerService extends BaseService
{
    public function createManager(ManagerInput $input)
    {
        $manager = ShopManager::new();
        $manager->shop_id = $input->shopId;
        $manager->user_id = $input->userId;
        $manager->avatar = $input->avatar;
        $manager->nickname = $input->nickname;

        return $this->updateManager($manager, $input->roleId);
    }

    public function updateManager(ShopManager $manager, $roleId)
    {
        $manager->role_id = $roleId;
        $manager->save();

        return $manager;
    }

    public function getManagerList($shopId, $columns = ['*'])
    {
        return ShopManager::query()->where('shop_id', $shopId)->get($columns);
    }

    public function getShopManager($shopId, $id, $columns = ['*'])
    {
        return ShopManager::query()->where('shop_id', $shopId)->find($id, $columns);
    }

    public function getManagerByUserId($shopId, $userId, $columns = ['*'])
    {
        return ShopManager::query()
            ->where('shop_id', $shopId)
            ->where('user_id', $userId)
            ->first($columns);
    }
}
