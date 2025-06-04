<?php

namespace App\Utils\Inputs;

class GoodsPageInput extends PageInput
{
    public $goodsIds;
    public $name;
    public $status;
    public $shopCategoryId;
    public $categoryId;
    public $shopId;
    public $sort;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'name' => 'string',
            'status' => 'integer|in:0,1,2,3',
            'goodsIds' => 'array',
            'shopCategoryId' => 'integer|digits_between:1,20',
            'categoryId' => 'integer|digits_between:1,20',
            'shopId' => 'integer|digits_between:1,20',
            'sort' => 'string',
        ]);
    }
}
