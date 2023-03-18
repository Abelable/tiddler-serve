<?php

namespace App\Utils\Inputs;

class TourismNoteInput extends BaseInput
{
    public $imageList;
    public $title;
    public $content;
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
            'goodsId' => 'integer|digits_between:1,20',
            'longitude' => 'numeric',
            'latitude' => 'numeric',
            'address' => 'string',
            'isPrivate' => 'integer|in:0,1'
        ];
    }
}
