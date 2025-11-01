<?php

namespace App\Utils\Inputs;

class TypePageInput extends PageInput
{
    public $scene;
    public $type;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'scene' => 'integer',
            'type' => 'integer',
        ]);
    }
}
