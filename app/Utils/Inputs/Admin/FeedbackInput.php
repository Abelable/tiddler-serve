<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

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
