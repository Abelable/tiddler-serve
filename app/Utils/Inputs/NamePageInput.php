<?php

namespace App\Utils\Inputs;

class NamePageInput extends PageInput
{
    public $name;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'name' => 'string',
        ]);
    }
}
