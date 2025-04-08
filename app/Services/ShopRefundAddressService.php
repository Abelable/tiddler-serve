<?php

namespace App\Services;

use App\Models\ShopRefundAddress;

class ShopRefundAddressService extends BaseService
{
    public function getListByShopId($shopId, $columns = ['*'])
    {
        return ShopRefundAddress::query()->where('shop_id', $shopId)->get($columns);
    }

    public function getAddressById($id, $columns = ['*'])
    {
        return ShopRefundAddress::query()->find($id, $columns);
    }
}
