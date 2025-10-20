<?php

namespace App\Utils\Inputs;

class IncomeWithdrawalPageInput extends PageInput
{
    public $merchantType;
    public $shopId;
    public $status;
    public $path;

    public function rules()
    {
        return array_merge([
            'merchantType' => 'integer|in:1,2,3,4',
            'shopId' => 'integer|digits_between:1,20',
            'status' => 'integer|in:0,1,2',
            'path' => 'integer|in:1,2,3',
        ], parent::rules());
    }
}
