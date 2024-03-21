<?php

namespace App\Utils\Inputs;

class TourismNoteInput extends BaseInput
{
    public $imageList;
    public $title;
    public $content;
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
            'imageList' => 'required|string',
            'title' => 'required|string',
            'content' => 'required|string',
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
