<?php

namespace App\Services;

use App\Models\ScenicManager;

class ScenicManagerService extends BaseService
{
    public function createManager($scenicId, $userId)
    {
        $address = ScenicManager::new();
        $address->scenic_id = $scenicId;
        $address->user_id = $userId;
        $address->save();
        return $address;
    }

    public function getManagerList($scenicId, $columns = ['*'])
    {
        return ScenicManager::query()->where('scenic_id', $scenicId)->get($columns);
    }

    public function deleteManager($scenicId)
    {
        ScenicManager::query()->where('scenic_id', $scenicId)->delete();
    }
}
