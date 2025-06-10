<?php

namespace App\Services;

use App\Models\ShopRefundAddress;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\ShopRefundAddressInput;

class ShopRefundAddressService extends BaseService
{
    public function getSelfList(PageInput $input, $columns = ['*'])
    {
        return ShopRefundAddress::query()
            ->where('shop_id', 0)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getSelfOptions($columns = ['*'])
    {
        return ShopRefundAddress::query()
            ->where('shop_id', 0)
            ->orderBy('id', 'asc')
            ->get($columns);
    }

    public function getListByShopId($shopId, $columns = ['*'])
    {
        return ShopRefundAddress::query()->where('shop_id', $shopId)->get($columns);
    }

    public function getAddressById($id, $columns = ['*'])
    {
        return ShopRefundAddress::query()->find($id, $columns);
    }

    public function update(ShopRefundAddress $address, ShopRefundAddressInput $input)
    {
        $address->consignee_name = $input->consigneeName;
        $address->mobile = $input->mobile;
        $address->address_detail = $input->addressDetail;
        $address->supplement = $input->supplement;
        $address->save();

        return $address;
    }
}
