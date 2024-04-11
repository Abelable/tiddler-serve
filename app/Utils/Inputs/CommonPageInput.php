<?php

namespace App\Utils\Inputs;

class CommonPageInput extends PageInput
{
    public $commodityIds;
    public $keywords;
    public $categoryId;
    public $sort;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'commodityIds' => 'array',
            'keywords' => 'string',
            'categoryId' => 'integer|digits_between:1,20',
            'sort' => 'string',
        ]);
    }
}
