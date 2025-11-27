<?php

namespace App\Services;

use App\Models\ShopPickupAddress;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\ShopPickupAddressInput;

class ShopPickupAddressService extends BaseService
{
    public function getSelfList(PageInput $input, $columns = ['*'])
    {
        return ShopPickupAddress::query()
            ->where('shop_id', 0)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getSelfOptions($columns = ['*'])
    {
        return ShopPickupAddress::query()
            ->where('shop_id', 0)
            ->orderBy('id', 'asc')
            ->get($columns);
    }

    public function getListByShopId($shopId, $columns = ['*'])
    {
        return ShopPickupAddress::query()->where('shop_id', $shopId)->get($columns);
    }

    public function getPageByShopId($shopId, PageInput $input, $columns = ['*'])
    {
        return ShopPickupAddress::query()
            ->where('shop_id', $shopId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getAddressById($id, $columns = ['*'])
    {
        return ShopPickupAddress::query()->find($id, $columns);
    }

    public function getListByIds(array $ids, $columns = ['*'])
    {
        return ShopPickupAddress::query()->whereIn('id', $ids)->get($columns);
    }

    public function update(ShopPickupAddress $address, ShopPickupAddressInput $input)
    {
        $address->logo = $input->logo ?? '';
        $address->name = $input->name;
        $address->open_time_list = json_encode($input->openTimeList);
        $address->longitude = $input->longitude;
        $address->latitude = $input->latitude;
        $address->address_detail = $input->addressDetail;
        $address->save();

        return $address;
    }
}
