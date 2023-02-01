<?php

namespace App\Utils\Inputs;

class GoodsEditInput extends GoodsAddInput
{
    public $id;

    public function rules()
    {
        return array_merge([
            'id' => 'required|integer|digits_between:1,20',
        ], parent::rules());
    }
}
