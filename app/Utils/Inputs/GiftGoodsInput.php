<?php

namespace App\Utils\Inputs;

class GiftGoodsInput extends BaseInput
{
    public $goodsIds;
    public $typeId;
    public $effectiveDuration;

    public function rules()
    {
        return [
            'goodsIds' => 'required|array|min:1',
            'typeId' => 'required|integer',
            'effectiveDuration' => 'required|integer',
        ];
    }
}
