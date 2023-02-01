<?php

namespace App\Services;


use App\Models\GoodsReturnAddress;

class GoodsReturnAddressService extends BaseService
{
    public function getListByUserId($userId, $columns = ['*'])
    {
        return GoodsReturnAddress::query()->where('user_id', $userId)->get($columns);
    }

    public function getAddressById($id, $columns = ['*'])
    {
        return GoodsReturnAddress::query()->find($id, $columns);
    }
}
