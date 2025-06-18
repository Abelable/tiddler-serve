<?php

namespace App\Utils\Inputs;

class ShopInput extends BaseInput
{
    public $bg;
    public $logo;
    public $name;
    public $brief;
    public $owner_name;
    public $mobile;
    public $addressDetail;
    public $longitude;
    public $latitude;
    public $openTimeList;

    public function rules()
    {
        return [
            'bg' => 'string',
            'logo' => 'string',
            'name' => 'string',
            'brief' => 'string',
            'owner_name' => 'string',
            'mobile' => 'regex:/^1[345789][0-9]{9}$/',
            'addressDetail' => 'required|string',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'openTimeList' => 'array',
        ];
    }
}
