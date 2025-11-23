<?php

namespace App\Utils\Inputs;

class RefundPageInput extends PageInput
{
    public $status;
    public $orderSn;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'status' => 'integer',
            'orderSn' => 'string'
        ]);
    }
}
