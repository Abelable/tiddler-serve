<?php

namespace App\Utils\Inputs;

class ProductMediaPageInput extends PageInput
{
    public $productType;
    public $productId;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'productType' => 'required|integer|digits_between:1,2,3,4',
            'productId' => 'required|integer|digits_between:1,20',
        ]);
    }
}
