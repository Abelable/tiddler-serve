<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class ShopPageInput extends PageInput
{
    public $name;
    public $type;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'name' => 'string',
            'type' => 'integer'
        ]);
    }
}
