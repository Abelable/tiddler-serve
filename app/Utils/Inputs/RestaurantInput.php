<?php

namespace App\Utils\Inputs;

class RestaurantInput extends BaseInput
{
    public $categoryId;
    public $name;
    public $price;
    public $cover;
    public $video;
    public $foodImageList;
    public $environmentImageList;
    public $priceImageList;
    public $longitude;
    public $latitude;
    public $address;
    public $telList;
    public $openTimeList;
    public $facilityList;

    public function rules()
    {
        return [
            'categoryId' => 'required|integer|digits_between:1,20',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'video' => 'string',
            'cover' => 'required|string',
            'foodImageList' => 'array',
            'environmentImageList' => 'array',
            'priceImageList' => 'array',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'address' => 'required|string',
            'telList' => 'array',
            'openTimeList' => 'array',
            'facilityList' => 'array',
        ];
    }
}
