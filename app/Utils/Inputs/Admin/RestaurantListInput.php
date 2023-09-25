<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class RestaurantListInput extends PageInput
{
    public $name;
    public $categoryId;

    public function rules()
    {
        return array_merge([
            'name' => 'string',
            'categoryId' => 'integer|digits_between:1,20',
        ], parent::rules());
    }
}
