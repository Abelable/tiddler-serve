<?php

namespace App\Utils\Inputs;

class GoodsReturnAddressAddInput extends BaseInput
{
    public $consigneeName;
    public $mobile;
    public $address;
    public $supplement;

    public function rules()
    {
        return [
            'consigneeName' => 'required|string',
            'mobile' => 'required|string',
            'address' => 'required|string',
            'supplement' => 'string',
        ];
    }
}
