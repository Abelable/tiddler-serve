<?php

namespace App\Utils\Inputs;

class TourismNoteInput extends BaseInput
{
    public $imageList;
    public $title;
    public $content;
    public $goodsId;

    public function rules()
    {
        return [
            'imageList' => 'required|string',
            'title' => 'required|string',
            'content' => 'string',
            'goodsId' => 'integer|digits_between:1,20',
        ];
    }
}
