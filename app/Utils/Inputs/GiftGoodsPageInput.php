<?php

namespace App\Utils\Inputs;

class GiftGoodsPageInput extends PageInput
{
    public $typeId;
    public $goodsId;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'typeId' => 'integer',
            'goodsId' => 'integer',
        ]);
    }
}
