<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class ShopCategoryInput extends BaseInput
{
    public $name;
    public $deposit;
    public $adaptedMerchantTypes;

    public function rules()
    {
        return [
            'name' => 'required|string',
            'deposit' => 'required|numeric',
            'adaptedMerchantTypes' => 'required|array|min:1',
        ];
    }
}
