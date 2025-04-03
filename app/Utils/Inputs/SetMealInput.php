<?php

namespace App\Utils\Inputs;

class SetMealInput extends BaseInput
{
    public $restaurantIds;
    public $cover;
    public $name;
    public $price;
    public $originalPrice;
    public $salesCommissionRate;
    public $promotionCommissionRate;
    public $promotionCommissionUpperLimit;
    public $packageDetails;
    public $validityDays;
    public $validityStartTime;
    public $validityEndTime;
    public $buyLimit;
    public $perTableUsageLimit;
    public $useTimeList;
    public $needPreBook;
    public $useRules;

    public function rules()
    {
        return [
            'restaurantIds' => 'required|array|min:1',
            'cover' => 'required|string',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'originalPrice' => 'numeric',
            'salesCommissionRate' => 'numeric',
            'promotionCommissionRate' => 'numeric',
            'promotionCommissionUpperLimit' => 'numeric',
            'packageDetails' => 'required|array|min:1',
            'validityDays' => 'integer',
            'validityStartTime' => 'string',
            'validityEndTime' => 'string',
            'buyLimit' => 'integer',
            'perTableUsageLimit' => 'integer',
            'useTimeList' => 'array',
            'needPreBook' => 'integer',
            'useRules' => 'array',
        ];
    }
}
