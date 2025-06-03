<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class ShortVideoPageInput extends PageInput
{
    public $name;

    public function rules()
    {
        return array_merge([
            'name' => 'string',
        ], parent::rules());
    }
}
