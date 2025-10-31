<?php

namespace App\Utils\Inputs;

class FeedbackInput extends BaseInput
{
    public $content;
    public $imageList;
    public $mobile;

    public function rules()
    {
        return [
            'content' => 'required|string',
            'imageList' => 'array',
            'mobile' => 'string',
        ];
    }
}
