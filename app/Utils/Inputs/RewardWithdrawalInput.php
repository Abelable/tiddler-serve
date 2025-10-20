<?php

namespace App\Utils\Inputs;

class RewardWithdrawalInput extends BaseInput
{
    public $taskId;
    public $amount;
    public $path;
    public $remark;

    public function rules()
    {
        return [
            'taskId' => 'integer|digits_between:1,20',
            'amount' => 'required|numeric',
            'path' => 'required|integer|in:1,2,3',
            'remark' => 'string',
        ];
    }
}
