<?php

namespace App\Utils\Inputs;

class HotelRoomInput extends BaseInput
{
    public $hotelId;
    public $typeId;
    public $price;
    public $priceList;
    public $salesCommissionRate;
    public $promotionCommissionRate;
    public $promotionCommissionUpperLimit;
    public $superiorPromotionCommissionRate;
    public $superiorPromotionCommissionUpperLimit;
    public $breakfastNum;
    public $guestNum;
    public $cancellable;

    public function rules()
    {
        return [
            'hotelId' => 'required|integer|digits_between:1,20',
            'typeId' => 'required|integer|digits_between:1,20',
            'price' => 'required|numeric',
            'priceList' => 'required|array|min:1',
            'salesCommissionRate' => 'numeric',
            'promotionCommissionRate' => 'numeric',
            'promotionCommissionUpperLimit' => 'numeric',
            'superiorPromotionCommissionRate' => 'numeric',
            'superiorPromotionCommissionUpperLimit' => 'numeric',
            'breakfastNum' => 'required|integer',
            'guestNum' => 'required|integer',
            'cancellable' => 'required|integer|in:0,1',
        ];
    }
}
