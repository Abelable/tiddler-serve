<?php

namespace App\Utils\Inputs;

class MealTicketInput extends BaseInput
{
    public $restaurantIds;
    public $price;
    public $originalPrice;
    public $salesCommissionRate;
    public $promotionCommissionRate;
    public $validityDays;
    public $validityStartTime;
    public $validityEndTime;
    public $buyLimit;
    public $perTableUsageLimit;
    public $overlayUsageLimit;
    public $useTimeList;
    public $inapplicableProducts;
    public $boxAvailable;
    public $needPreBook;
    public $useRules;

    public function rules()
    {
        return [
            'restaurantIds' => 'required|array|min:1',
            'price' => 'required|numeric',
            'originalPrice' => 'numeric',
            'salesCommissionRate' => 'required|numeric',
            'promotionCommissionRate' => 'required|numeric',
            'validityDays' => 'integer',
            'validityStartTime' => 'string',
            'validityEndTime' => 'string',
            'buyLimit' => 'integer',
            'perTableUsageLimit' => 'integer',
            'overlayUsageLimit' => 'integer',
            'useTimeList' => 'array',
            'inapplicableProducts' => 'array',
            'boxAvailable' => 'integer',
            'needPreBook' => 'integer',
            'useRules' => 'array',
        ];
    }
}
