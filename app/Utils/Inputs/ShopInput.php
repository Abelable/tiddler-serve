<?php

namespace App\Utils\Inputs;

class ShopInput extends BaseInput
{
    public $bg;
    public $logo;
    public $name;
    public $brief;
    public $ownerName;
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
            'ownerName' => 'string',
            'mobile' => 'regex:/^1[3-9]\d{9}$/',
            'addressDetail' => 'string',
            'longitude' => 'numeric',
            'latitude' => 'numeric',
            'openTimeList' => 'array',
        ];
    }
}
