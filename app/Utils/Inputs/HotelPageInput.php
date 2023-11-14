<?php

namespace App\Utils\Inputs;

class HotelPageInput extends PageInput
{
    public $categoryId;
    public $sort;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'categoryId' => 'integer|digits_between:1,20',
            'sort' => 'string',
        ]);
    }
}
