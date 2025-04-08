<?php

namespace App\Utils\Inputs;

class ShopPickupAddressInput extends BaseInput
{
    public $name;
    public $timeFrame;
    public $addressDetail;
    public $longitude;
    public $latitude;

    public function rules()
    {
        return [
            'name' => 'string',
            'timeFrame' => 'string',
            'addressDetail' => 'required|string',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
        ];
    }
}
