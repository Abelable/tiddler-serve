<?php

namespace App\Utils\Inputs;

class NewYearGoodsInput extends BaseInput
{
    public $goodsIds;
    public $luckScore;

    public function rules()
    {
        return [
            'goodsIds' => 'required|array|min:1',
            'luckScore' => 'required|integer',
        ];
    }
}
