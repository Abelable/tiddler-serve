<?php

namespace App\Utils\Inputs;

class GiftGoodsPageInput extends PageInput
{
    public $typeId;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'typeId' => 'integer',
        ]);
    }
}
