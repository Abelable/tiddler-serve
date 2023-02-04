<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class ShopListInput extends PageInput
{
    public $name;
    public $categoryId;

    public function rules()
    {
        return array_merge([
            'name' => 'string',
            'categoryId' => 'integer'
        ], parent::rules());
    }
}
