<?php

namespace App\Utils\Inputs;

class MerchantGoodsListInput extends PageInput
{
    public $status;

    public function rules()
    {
        return array_merge([
            'status' => 'required|integer|in:0,1,2,3',
        ], parent::rules());
    }
}
