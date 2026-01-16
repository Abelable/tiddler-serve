<?php

namespace App\Utils\Inputs\Activity;

use App\Utils\Inputs\BaseInput;

class NewYearPrizeInput extends BaseInput
{
    public $type;
    public $couponId;
    public $goodsId;
    public $isBig;
    public $cover;
    public $name;

    public function rules()
    {
        return [
            'type' => 'required|integer|in:1,2,3',
            'couponId' => 'integer|digits_between:1,20',
            'goodsId' => 'integer|digits_between:1,20',
            'isBig' => 'integer|in:0,1',
            'cover' => 'required|string',
            'name' => 'required|string',
        ];
    }
}
