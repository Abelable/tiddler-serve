<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class GoodsApproveInput extends BaseInput
{
    public $id;
    public $promotionCommissionRate;
    public $promotionCommissionUpperLimit;
    public $superiorPromotionCommissionRate;
    public $superiorPromotionCommissionUpperLimit;

    public function rules()
    {
        return [
            'id' => 'required|integer|digits_between:1,20',
            'promotionCommissionRate' => 'numeric',
            'promotionCommissionUpperLimit' => 'numeric',
            'superiorPromotionCommissionRate' => 'numeric',
            'superiorPromotionCommissionUpperLimit' => 'numeric',
        ];
    }
}
