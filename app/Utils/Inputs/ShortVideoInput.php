<?php

namespace App\Utils\Inputs;

class ShortVideoInput extends BaseInput
{
    public $title;
    public $cover;
    public $videoUrl;
    public $goodsId;

    public function rules()
    {
        return [
            'title' => 'required|string',
            'cover' => 'required|string',
            'videoUrl' => 'required|string',
            'goodsId' => 'integer|digits_between:1,20',
            'longitude' => 'numeric',
            'latitude' => 'numeric',
            'address' => 'string',
            'isPrivate' => 'integer|in:0,1'
        ];
    }
}
