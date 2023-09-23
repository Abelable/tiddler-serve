<?php

namespace App\Utils\Inputs;

class RestaurantInput extends BaseInput
{
    public $categoryId;
    public $name;
    public $price;
    public $logo;
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
            'logo' => 'required|string',
            'cover' => 'required|string',
            'foodImageList' => 'required|array',
            'environmentImageList' => 'required|array',
            'priceImageList' => 'required|array',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'address' => 'required|string',
            'telList' => 'required|array',
            'openTimeList' => 'array',
            'facilityList' => 'array',
        ];
    }
}
