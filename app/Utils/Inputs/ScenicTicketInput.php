<?php

namespace App\Utils\Inputs;

class ScenicTicketInput extends BaseInput
{
    public $type;
    public $scenicIds;
    public $name;
    public $briefName;
    public $price;
    public $marketPrice;
    public $specList;
    public $salesCommissionRate;
    public $promotionCommissionRate;
    public $promotionCommissionUpperLimit;
    public $superiorPromotionCommissionRate;
    public $superiorPromotionCommissionUpperLimit;
    public $feeIncludeTips;
    public $feeNotIncludeTips;
    public $bookingTime;
    public $effectiveTime;
    public $validityTime;
    public $limitNumber;
    public $refundStatus;
    public $refundTips;
    public $needExchange;
    public $exchangeTips;
    public $exchangeTime;
    public $exchangeLocation;
    public $enterTime;
    public $enterLocation;
    public $invoiceTips;
    public $reminderTips;

    public function rules()
    {
        return [
            'type' => 'required|integer|in:1,2',
            'scenicIds' => 'required|array|min:1',
            'name' => 'required|string',
            'briefName' => 'required|string',
            'price' => 'required|numeric',
            'marketPrice' => 'numeric',
            'specList' => 'required|array|min:1',
            'salesCommissionRate' => 'numeric',
            'promotionCommissionRate' => 'numeric',
            'promotionCommissionUpperLimit' => 'numeric',
            'superiorPromotionCommissionRate' => 'numeric',
            'superiorPromotionCommissionUpperLimit' => 'numeric',
            'feeIncludeTips' => 'string',
            'feeNotIncludeTips' => 'string',
            'bookingTime' => 'required|string',
            'effectiveTime' => 'integer',
            'validityTime' => 'integer',
            'limitNumber' => 'integer',
            'refundStatus' => 'required|integer|in:1,2,3',
            'refundTips' => 'string',
            'needExchange' => 'required|integer|in:0,1',
            'exchangeTips' => 'string',
            'exchangeTime' => 'string',
            'exchangeLocation' => 'string',
            'enterTime' => 'string',
            'enterLocation' => 'string',
            'invoiceTips' => 'string',
            'reminderTips' => 'string',
        ];
    }
}
