<?php

namespace App\Utils\Inputs;

class ComplaintInput extends BaseInput
{
    public $optionIds;
    public $content;
    public $imageList;

    public function rules()
    {
        return [
            'optionIds' => 'string',
            'content' => 'string',
            'imageList' => 'array',
        ];
    }
}
