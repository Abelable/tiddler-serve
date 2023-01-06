<?php

namespace App\Utils\Inputs;

class ShopListInput extends PageInput
{
    public $name;

    public function rules()
    {
        return array_merge([
            'name' => 'string',
        ], parent::rules());
    }
}
