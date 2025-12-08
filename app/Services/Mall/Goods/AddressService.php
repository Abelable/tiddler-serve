<?php

namespace App\Services\Mall\Goods;

use App\Models\Mall\Goods\Address;
use App\Services\BaseService;
use App\Utils\Inputs\AddressInput;

class AddressService extends BaseService
{
    public function getList($userId, $columns = ['*'])
    {
        return Address::query()
            ->where('user_id', $userId)
            ->orderByRaw("CASE WHEN is_default = 1 THEN 0 ELSE 1 END")
            ->get($columns);
    }

    public function getById($userId, $id, $columns = ['*'])
    {
        return Address::query()->where('user_id', $userId)->where('id', $id)->first($columns);
    }

    public function getUserAddressById($userId, $id, $columns=['*'])
    {
        return Address::query()->where('user_id', $userId)->where('id', $id)->first($columns);
    }

    public function updateAddress(Address $address, AddressInput $input)
    {
        if ($input->isDefault == 1 && $address->is_default == 0) {
            $this->resetDefaultAddress($address->user_id);
        }

        $address->name = $input->name;
        $address->mobile = $input->mobile;
        $address->region_desc = $input->regionDesc;
        $address->region_code_list = $input->regionCodeList;
        $address->address_detail = $input->addressDetail;
        $address->is_default = $input->isDefault;
        $address->save();

        return $address;
    }

    public function getDefaultAddress($userId, $columns = ['*'])
    {
        $address = $this->getUserDefaultAddress($userId, $columns);
        if (is_null($address)) {
            $address = Address::query()->where('user_id', $userId)->first($columns);
        }
        return $address;
    }

    public function resetDefaultAddress($userId, $columns = ['*'])
    {
        $address = $this->getUserDefaultAddress($userId, $columns);
        if (!is_null($address)) {
            $address->is_default = 0;
            $address->save();
        }
        return $address;
    }

    public function getUserDefaultAddress($userId, $columns = ['*'])
    {
        return Address::query()->where('user_id', $userId)->where('is_default', 1)->first($columns);
    }
}
