<?php

namespace App\Utils\Inputs;

class ShopGoodsListInput extends PageInput
{
    public $shopId;

    public function rules()
    {
        return array_merge([
            'shopId' => 'required|integer|digits_between:1,20',
        ], parent::rules());
    }
}
