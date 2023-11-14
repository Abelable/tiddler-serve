<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class HotelPageInput extends PageInput
{
    public $name;
    public $status;
    public $grade;
    public $categoryId;

    public function rules()
    {
        return array_merge([
            'name' => 'string',
            'status' => 'integer|in:0,1,2,3',
            'grade' => 'integer|in:1,2,3,4',
            'categoryId' => 'integer|digits_between:1,20',
        ], parent::rules());
    }
}
