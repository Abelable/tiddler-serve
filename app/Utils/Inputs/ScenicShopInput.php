<?php

namespace App\Utils\Inputs;

class ScenicShopInput extends BaseInput
{
    public $bg;
    public $logo;
    public $name;
    public $brief;
    public $ownerName;
    public $mobile;

    public function rules()
    {
        return [
            'bg' => 'string',
            'logo' => 'string',
            'name' => 'string',
            'brief' => 'string',
            'ownerName' => 'string',
            'mobile' => 'regex:/^1[345789][0-9]{9}$/',
        ];
    }
}
