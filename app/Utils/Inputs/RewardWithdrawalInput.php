<?php

namespace App\Utils\Inputs;

class RewardWithdrawalInput extends BaseInput
{
    public $amount;
    public $path;
    public $remark;

    public function rules()
    {
        return [
            'amount' => 'required|numeric',
            'path' => 'required|integer|in:1,2,3',
            'remark' => 'string',
        ];
    }
}
