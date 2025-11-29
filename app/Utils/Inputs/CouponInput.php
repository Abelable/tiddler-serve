<?php

namespace App\Utils\Inputs;

class CouponInput extends BaseInput
{
    public $shopId;
    public $name;
    public $denomination;
    public $description;
    public $goodsIds;
    public $type;
    public $numLimit;
    public $priceLimit;
    public $receiveLimit;
    public $expirationTime;

    public function rules()
    {
        return [
            'shopId' => 'integer|digits_between:1,20',
            'name' => 'required|string',
            'denomination' => 'required|numeric',
            'description' => 'required|string',
            'goodsIds' => 'required|array',
            'type' => 'required|integer|in:1,2,3',
            'numLimit' => 'integer|digits_between:1,20',
            'priceLimit' => 'numeric',
            'receiveLimit' => 'integer|digits_between:1,20',
            'expirationTime' => 'string',
        ];
    }
}
