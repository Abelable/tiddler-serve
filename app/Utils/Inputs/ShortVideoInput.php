<?php

namespace App\Utils\Inputs;

class ShortVideoInput extends BaseInput
{
    public $title;
    public $cover;
    public $videoUrl;
    public $commodityList;
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
            'longitude' => 'numeric',
            'latitude' => 'numeric',
            'address' => 'string',
            'isPrivate' => 'integer|in:0,1'
        ];
    }
}
