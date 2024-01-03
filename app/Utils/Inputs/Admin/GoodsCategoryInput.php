<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class GoodsCategoryInput extends BaseInput
{
    public $shopCategoryId;
    public $name;
    public $minSalesCommissionRate;
    public $maxSalesCommissionRate;
    public $minPromotionCommissionRate;
    public $maxPromotionCommissionRate;

    public function rules()
    {
        return [
            'shopCategoryId' => 'required|integer|digits_between:1,20',
            'name' => 'required|string',
            'minSalesCommissionRate' => 'required|integer',
            'maxSalesCommissionRate' => 'required|integer',
            'minPromotionCommissionRate' => 'required|integer',
            'maxPromotionCommissionRate' => 'required|integer',
        ];
    }
}
