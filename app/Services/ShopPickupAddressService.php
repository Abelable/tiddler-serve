<?php

namespace App\Services;

use App\Models\ShopPickupAddress;

class ShopPickupAddressService extends BaseService
{
    public function getListByShopId($shopId, $columns = ['*'])
    {
        return ShopPickupAddress::query()->where('shop_id', $shopId)->get($columns);
    }

    public function getAddressById($id, $columns = ['*'])
    {
        return ShopPickupAddress::query()->find($id, $columns);
    }
}
