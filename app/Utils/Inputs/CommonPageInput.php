<?php

namespace App\Utils\Inputs;

class CommonPageInput extends PageInput
{
    public $productIds;
    public $keywords;
    public $categoryId;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'productIds' => 'array',
            'keywords' => 'string',
            'categoryId' => 'integer|digits_between:1,20',
        ]);
    }
}
