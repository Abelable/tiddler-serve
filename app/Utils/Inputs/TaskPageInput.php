<?php

namespace App\Utils\Inputs;

class TaskPageInput extends PageInput
{
    public $status;
    public $merchantType;
    public $merchantName;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'status' => 'integer',
            'merchantType' => 'integer',
            'merchantName' => 'string',
        ]);
    }
}
