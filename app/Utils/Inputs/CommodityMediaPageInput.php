<?php

namespace App\Utils\Inputs;

class CommodityMediaPageInput extends PageInput
{
    public $commodityType;
    public $commodityId;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'commodityType' => 'required|integer|digits_between:1,2,3,4',
            'commodityId' => 'required|integer|digits_between:1,20',
        ]);
    }
}
