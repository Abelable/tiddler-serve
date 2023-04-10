<?php

namespace App\Services;

use App\Models\ScenicShop;

class ScenicShopService extends BaseService
{
    public function getShopById(int $id, $columns = ['*'])
    {
        return ScenicShop::query()->find($id, $columns);
    }

    public function getShopByUserId(int $userId, $columns = ['*'])
    {
        return ScenicShop::query()->where('user_id', $userId)->first($columns);
    }
}
