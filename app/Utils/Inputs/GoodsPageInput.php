<?php

namespace App\Utils\Inputs;

use Illuminate\Validation\Rule;

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
