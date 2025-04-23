<?php

namespace App\Utils\Inputs;

class ShopPickupAddressInput extends BaseInput
{
    public $name;
    public $addressDetail;
    public $longitude;
    public $latitude;
    public $openTimeList;

    public function rules()
    {
        return [
            'name' => 'string',
            'addressDetail' => 'required|string',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'openTimeList' => 'array',
        ];
    }
}
