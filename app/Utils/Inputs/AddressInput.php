<?php

namespace App\Utils\Inputs;

class AddressInput extends BaseInput
{
    public $id;
    public $isDefault;
    public $name;
    public $mobile;
    public $regionDesc;
    public $regionCodeList;
    public $addressDetail;

    public function rules()
    {
        return [
            'id' => 'integer|digits_between:1,20',
            'isDefault' => 'integer|in:0,1',
            'name' => 'required|string',
            'mobile' => 'required|regex:/^1[345789][0-9]{9}$/',
            'regionDesc' => 'required|string',
            'regionCodeList' => 'required|string',
            'addressDetail' => 'required|string',
        ];
    }
}
