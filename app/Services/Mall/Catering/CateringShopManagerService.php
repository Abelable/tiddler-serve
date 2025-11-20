<?php

namespace App\Services\Mall\Catering;

use App\Models\Catering\CateringShopManager;
use App\Models\User;
use App\Services\BaseService;
use App\Utils\Inputs\ManagerInput;
use App\Utils\Inputs\ManagerPageInput;

class CateringShopManagerService extends BaseService
{
    public function createManager(ManagerInput $input, User $user)
    {
        $manager = CateringShopManager::new();
        $manager->shop_id = $input->shopId;
        $manager->user_id = $input->userId;
        $manager->avatar = $user->avatar;
        $manager->nickname = $user->nickname;
        $manager->mobile = $user->mobile;

        return $this->updateManager($manager, $input->roleId);
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

    public function getManagerPage($shopId, ManagerPageInput $input, $columns = ['*'])
    {
        $query = CateringShopManager::new()->where('shop_id', $shopId);
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
