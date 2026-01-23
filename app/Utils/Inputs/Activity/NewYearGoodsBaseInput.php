<?php

namespace App\Utils\Inputs\Activity;

use App\Utils\Inputs\BaseInput;

class NewYearGoodsBaseInput extends BaseInput
{
    public $goodsIds;
    public $luckScore;
    public $stock;
    public $limit;

    public function rules()
    {
        return [
            'goodsIds' => 'required|array|min:1',
            'luckScore' => 'required|integer',
            'stock' => 'integer',
            'limit' => 'integer',
        ];
    }
}
