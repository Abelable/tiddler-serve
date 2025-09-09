<?php

namespace App\Utils\Inputs;

class SetMealOrderInput extends BaseInput
{
    public $restaurantId;
    public $restaurantCover;
    public $restaurantName;
    public $setMealId;
    public $num;
    public $useBalance;

    public function rules()
    {
        return [
            'restaurantId' => 'required|integer|digits_between:1,20',
            'restaurantCover' => 'required|string',
            'restaurantName' => 'required|string',
            'setMealId' => 'required|integer|digits_between:1,20',
            'num' => 'required|integer',
            'useBalance' => 'integer|in:0,1',
        ];
    }
}
