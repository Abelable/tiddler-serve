<?php

namespace App\Utils\Inputs;

class CartGoodsInput extends BaseInput
{
    public $goodsId;
    public $selectedSkuIndex;
    public $number;

    public function rules()
    {
        return [
            'goodsId' => 'required|integer|digits_between:1,20',
            'selectedSkuIndex' => 'required|integer',
            'number' => 'required|integer',
        ];
    }
}
