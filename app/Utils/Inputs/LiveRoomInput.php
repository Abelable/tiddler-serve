<?php

namespace App\Utils\Inputs;

class LiveRoomInput extends BaseInput
{
    public $title;
    public $cover;
    public $shareCover;
    public $resolution;
    public $direction;
    public $goodsIds;
    public $noticeTime;

    public function rules()
    {
        return [
            'title' => 'required|string',
            'cover' => 'required|string',
            'shareCover' => 'required|string',
            'resolution' => 'required|integer|in:1,2,3',
            'direction' => 'required|integer|in:1,2',
            'goodsIds' => 'array',
            'noticeTime' => 'string',
        ];
    }
}
