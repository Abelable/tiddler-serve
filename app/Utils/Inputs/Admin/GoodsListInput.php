<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class GoodsListInput extends PageInput
{
    public $name;
    public $status;
    public $shopCategoryId;
    public $categoryId;
    public $shopId;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'name' => 'string',
            'status' => 'integer|in:0,1,2,3',
            'shopCategoryId' => 'integer|digits_between:1,20',
            'categoryId' => 'integer|digits_between:1,20',
            'shopId' => 'integer|digits_between:1,20',
        ]);
    }
}
