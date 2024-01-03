<?php

namespace App\Utils\Inputs;

class GoodsPageInput extends PageInput
{
    public $shopCategoryId;
    public $categoryId;
    public $sort;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'shopCategoryId' => 'integer|digits_between:1,20',
            'categoryId' => 'integer|digits_between:1,20',
            'sort' => 'string',
        ]);
    }
}
