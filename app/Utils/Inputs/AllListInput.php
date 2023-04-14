<?php

namespace App\Utils\Inputs;

use Illuminate\Validation\Rule;

class AllListInput extends BaseInput
{
    public $name;
    public $categoryId;
    public $page = 1;
    public $limit = 10;
    public $sort;
    public $order = 'desc';

    public function rules()
    {
        return [
            'name' => 'string',
            'categoryId' => 'integer|digits_between:1,20',
            'page' => 'integer',
            'limit' => 'integer',
            'sort' => 'string',
            'order' => Rule::in(['desc', 'asc'])
        ];
    }
}
