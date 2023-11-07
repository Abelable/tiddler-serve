<?php

namespace App\Utils\Inputs;

class GoodsPageInput extends PageInput
{
    public $categoryId;
    public $sort;

    public function rules()
    {
        return [
            'categoryId' => 'integer|digits_between:1,20',
            'sort' => 'string',
        ];
    }
}
