<?php

namespace App\Utils\Inputs;

class ShortVideoInput extends BaseInput
{
    public $title;
    public $cover;
    public $videoUrl;
    public $commodityList;
    public $scenicIds;
    public $hotelIds;
    public $restaurantIds;
    public $goodsIds;
    public $longitude;
    public $latitude;
    public $address;
    public $isPrivate;

    public function rules()
    {
        return [
            'title' => 'required|string',
            'cover' => 'required|string',
            'videoUrl' => 'required|string',
            '$commodityList' => 'array',
            'scenicIds' => 'array',
            'hotelIds' => 'array',
            'restaurantIds' => 'array',
            'goodsIds' => 'array',
            'longitude' => 'numeric',
            'latitude' => 'numeric',
            'address' => 'string',
            'isPrivate' => 'integer|in:0,1'
        ];
    }
}
