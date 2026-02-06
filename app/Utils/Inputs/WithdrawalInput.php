<?php

namespace App\Utils\Inputs;

class WithdrawalInput extends BaseInput
{
    public $taskId;
    public $scene;
    public $amount;
    public $path;
    public $outBillNo;
    public $remark;

    public function rules()
    {
        return [
            'taskId' => 'integer|digits_between:1,20',
            'scene' => 'integer|in:1,2,3',
            'amount' => 'required|numeric',
            'path' => 'integer|in:1,2,3',
            'outBillNo' => 'string',
            'remark' => 'string',
        ];
    }
}
