<?php

namespace App\Utils\Inputs;

class LiveRoomInput extends BaseInput
{
    public $name;
    public $cover;
    public $shareCover;
    public $direction;
    public $goodsIds;
    public $noticeTime;

    public function rules()
    {
        return [
            'name' => 'required|string',
            'cover' => 'required|string',
            'shareCover' => 'required|string',
            'direction' => 'required|integer|in:1,2',
            'goodsIds' => 'array|min:1',
            'noticeTime' => 'string',
        ];
    }
}
