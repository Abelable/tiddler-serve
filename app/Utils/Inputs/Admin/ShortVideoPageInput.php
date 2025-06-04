<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class ShortVideoPageInput extends PageInput
{
    public $title;
    public $userId;

    public function rules()
    {
        return array_merge([
            'title' => 'string',
            'userId' => 'integer|digits_between:1,20',
        ], parent::rules());
    }
}
