<?php

namespace App\Utils\Inputs;

class TourismNoteInput extends BaseInput
{
    public $imageList;
    public $title;
    public $content;
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
            'imageList' => 'required|array',
            'title' => 'required|string',
            'content' => 'required|string',
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
