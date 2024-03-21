<?php

namespace App\Utils\Inputs;

class ShortVideoInput extends BaseInput
{
    public $title;
    public $cover;
    public $videoUrl;
    public $scenicId;
    public $hotelId;
    public $restaurantId;
    public $goodsId;
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
            'scenicId' => 'integer|digits_between:1,20',
            'hotelId' => 'integer|digits_between:1,20',
            'restaurantId' => 'integer|digits_between:1,20',
            'goodsId' => 'integer|digits_between:1,20',
            'longitude' => 'numeric',
            'latitude' => 'numeric',
            'address' => 'string',
            'isPrivate' => 'integer|in:0,1'
        ];
    }
}
