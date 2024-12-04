<?php

namespace App\Utils\Inputs;

class GiftGoodsPageInput extends PageInput
{
    public $type;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'type' => 'integer|in:1,2',
        ]);
    }
}
