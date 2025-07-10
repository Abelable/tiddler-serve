<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class CommissionInput extends BaseInput
{
    public $promotionCommissionRate;
    public $promotionCommissionUpperLimit;
    public $superiorPromotionCommissionRate;
    public $superiorPromotionCommissionUpperLimit;

    public function rules()
    {
        return [
            'promotionCommissionRate' => 'numeric',
            'promotionCommissionUpperLimit' => 'numeric',
            'superiorPromotionCommissionRate' => 'numeric',
            'superiorPromotionCommissionUpperLimit' => 'numeric',
        ];
    }
}
