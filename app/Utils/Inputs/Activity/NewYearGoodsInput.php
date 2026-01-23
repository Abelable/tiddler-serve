<?php

namespace App\Utils\Inputs\Activity;

use App\Utils\Inputs\BaseInput;

class NewYearGoodsInput extends BaseInput
{
    public $id;
    public $cover;
    public $name;
    public $luckScore;
    public $stock;
    public $limit;

    public function rules()
    {
        return [
            'ids' => 'required|integer|digits_between:1,20',
            'cover' => 'string',
            'name' => 'integer',
            'luckScore' => 'integer',
            'stock' => 'integer',
            'limit' => 'integer',
        ];
    }
}
