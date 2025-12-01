<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class GoodsCategoryInput extends BaseInput
{
    public $shopCategoryIds;
    public $logo;
    public $name;
    public $description;
    public $minSalesCommissionRate;
    public $maxSalesCommissionRate;
    public $minPromotionCommissionRate;
    public $maxPromotionCommissionRate;
    public $promotionCommissionUpperLimit;
    public $minSuperiorPromotionCommissionRate;
    public $maxSuperiorPromotionCommissionRate;
    public $superiorPromotionCommissionUpperLimit;

    public function rules()
    {
        return [
            'shopCategoryIds' => 'required|array',
            'logo' => 'string',
            'name' => 'required|string',
            'description' => 'string',
            'minSalesCommissionRate' => 'numeric',
            'maxSalesCommissionRate' => 'numeric',
            'minPromotionCommissionRate' => 'numeric',
            'maxPromotionCommissionRate' => 'numeric',
            'promotionCommissionUpperLimit' => 'numeric',
            'minSuperiorPromotionCommissionRate' => 'numeric',
            'maxSuperiorPromotionCommissionRate' => 'numeric',
            'superiorPromotionCommissionUpperLimit' => 'numeric',
        ];
    }
}
