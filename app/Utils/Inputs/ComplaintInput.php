<?php

namespace App\Utils\Inputs;

class ComplaintInput extends BaseInput
{
    public $promoterId;
    public $optionIds;
    public $content;
    public $imageList;

    public function rules()
    {
        return [
            'promoterId' => 'required|integer|digits_between:1,20',
            'optionIds' => 'string',
            'content' => 'string',
            'imageList' => 'array',
        ];
    }
}
