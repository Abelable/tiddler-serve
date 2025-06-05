<?php

namespace App\Utils\Inputs;

class GiftGoodsListInput extends BaseInput
{
    public $regionId;
    public $goodsIds;
    public $type;
    public $effectiveDuration;

    public function rules()
    {
        return [
            'regionId' => 'integer|digits_between:1,20',
            'goodsIds' => 'required|array|min:1',
            'type' => 'required|integer|in:1,2',
            'effectiveDuration' => 'required|integer',
        ];
    }
}
