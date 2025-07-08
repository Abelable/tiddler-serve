<?php

namespace App\Services;

use App\Models\ScenicManager;

class ScenicManagerService extends BaseService
{
    public function createManager($scenicId, $managerId)
    {
        $address = ScenicManager::new();
        $address->scenic_id = $scenicId;
        $address->manager_id = $managerId;
        $address->save();
        return $address;
    }

    public function getListByScenicId($scenicId, $columns = ['*'])
    {
        return ScenicManager::query()->where('scenic_id', $scenicId)->get($columns);
    }

    public function getListByManagerId($managerId, $columns = ['*'])
    {
        return ScenicManager::query()->where('manager_id', $managerId)->get($columns);
    }

    public function deleteManager($managerId)
    {
        return ScenicManager::query()->where('manager_id', $managerId)->delete();
    }
}
