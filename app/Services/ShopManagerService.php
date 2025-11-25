<?php

namespace App\Services;

use App\Models\ShopManager;
use App\Models\User;
use App\Utils\Inputs\ManagerInput;
use App\Utils\Inputs\ManagerPageInput;

class ShopManagerService extends BaseService
{
    public function createManager(ManagerInput $input, User $user)
    {
        $manager = ShopManager::new();
        $manager->shop_id = $input->shopId;
        $manager->user_id = $input->userId;
        $manager->avatar = $user->avatar;
        $manager->nickname = $user->nickname;
        $manager->mobile = $user->mobile;

        return $this->updateManager($manager, $input->roleId);
    }

    public function updateManager(ShopManager $manager, $roleId)
    {
        $manager->role_id = $roleId;
        $manager->save();

        return $manager;
    }

    public function getManagerPage($shopId, ManagerPageInput $input, $columns = ['*'])
    {
        $query = ShopManager::new()->where('shop_id', $shopId);
        if (!empty($input->nickname)) {
            $query = $query->where('nickname', $input->nickname);
        }
        if (!empty($input->mobile)) {
            $query = $query->where('mobile', $input->mobile);
        }
        if (!empty($input->roleId)) {
            $query = $query->where('role_id', $input->roleId);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getManagerList($shopId, $columns = ['*'])
    {
        return ShopManager::query()->where('shop_id', $shopId)->get($columns);
    }

    public function getManagerListByUserId($userId, $columns = ['*'])
    {
        return ShopManager::query()->where('user_id', $userId)->get($columns);
    }

    public function getManagerListByRoleIds($userId, array $roleIds, $columns = ['*'])
    {
        return ShopManager::query()
            ->where('user_id', $userId)
            ->whereIn('role_id', $roleIds)
            ->get($columns);
    }

    public function getShopManager($shopId, $id, $columns = ['*'])
    {
        return ShopManager::query()->where('shop_id', $shopId)->where('id', $id)->first($columns);
    }

    public function getManagerByUserId($shopId, $userId, $columns = ['*'])
    {
        return ShopManager::query()
            ->where('shop_id', $shopId)
            ->where('user_id', $userId)
            ->first($columns);
    }
}
