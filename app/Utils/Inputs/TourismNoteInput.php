<?php

namespace App\Utils\Inputs;

class TourismNoteInput extends BaseInput
{
    public $imageList;
    public $title;
    public $content;
    public $commodityList;
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
            '$commodityList' => 'array',
            'longitude' => 'numeric',
            'latitude' => 'numeric',
            'address' => 'string',
            'isPrivate' => 'integer|in:0,1'
        ];
    }
}
