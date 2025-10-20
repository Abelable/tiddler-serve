<?php

namespace App\Utils\Inputs;

class CommissionWithdrawalInput extends BaseInput
{
    public $scene;
    public $amount;
    public $path;
    public $remark;

    public function rules()
    {
        return [
            'scene' => 'required|integer|in:1,2,3',
            'amount' => 'required|numeric',
            'path' => 'required|integer|in:1,2,3',
            'remark' => 'string',
        ];
    }
}
