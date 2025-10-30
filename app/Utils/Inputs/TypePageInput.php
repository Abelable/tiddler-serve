<?php

namespace App\Utils\Inputs;

class TypePageInput extends PageInput
{
    public $type;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'type' => 'integer',
        ]);
    }
}
