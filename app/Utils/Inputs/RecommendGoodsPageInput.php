<?php

namespace App\Utils\Inputs;

class RecommendGoodsPageInput extends PageInput
{
    public $goodsIds;
    public $shopCategoryIds;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'goodsIds' => 'array',
            'shopCategoryIds' => 'array',
        ]);
    }
}
