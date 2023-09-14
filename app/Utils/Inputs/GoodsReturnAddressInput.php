<?php

namespace App\Utils\Inputs;

class GoodsReturnAddressInput extends BaseInput
{
    public $consigneeName;
    public $mobile;
    public $addressDetail;
    public $supplement;

    public function rules()
    {
        return [
            'consigneeName' => 'required|string',
            'mobile' => 'required|string',
            'addressDetail' => 'required|string',
            'supplement' => 'string',
        ];
    }
}
