<?php

namespace App\Utils\Inputs;

class TaskRewardWithdrawalInput extends BaseInput
{
    public $withdrawAmount;
    public $path;
    public $remark;

    public function rules()
    {
        return [
            'withdrawAmount' => 'required|numeric',
            'path' => 'required|integer|in:1,2,3',
            'remark' => 'string',
        ];
    }
}
