<?php

namespace App\Utils\Inputs;

class ScenicTicketInput extends BaseInput
{
    public $type;
    public $scenicIds;
    public $name;
    public $price;
    public $marketPrice;
    public $specList;
    public $salesCommissionRate;
    public $promotionCommissionRate;
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
    public $otherTips;

    public function rules()
    {
        return [
            'type' => 'required|integer|in:1,2',
            'scenicIds' => 'required|array|min:1',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'marketPrice' => 'numeric',
            'specList' => 'required|array|min:1',
            'salesCommissionRate' => 'required|numeric',
            'promotionCommissionRate' => 'required|numeric',
            'feeIncludeTips' => 'required|string',
            'feeNotIncludeTips' => 'string',
            'bookingTime' => 'required|string',
            'effectiveTime' => 'required|string',
            'validityTime' => 'required|string',
            'limitNumber' => 'integer',
            'refundStatus' => 'required|integer|in:1,2,3',
            'refundTips' => 'string',
            'needExchange' => 'required|integer|in:0,1',
            'exchangeTips' => 'string',
            'exchangeTime' => 'string',
            'exchangeLocation' => 'string',
            'otherTips' => 'string',
        ];
    }
}
