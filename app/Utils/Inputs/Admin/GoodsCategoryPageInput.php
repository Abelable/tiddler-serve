<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class GoodsCategoryPageInput extends PageInput
{
    public $shopCategoryId;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'shopCategoryId' => 'integer|digits_between:1,20',
        ]);
    }
}
