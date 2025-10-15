<?php

namespace App\Utils\Inputs;

class TaskPageInput extends PageInput
{
    public $status;
    public $productType;
    public $productName;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'status' => 'integer',
            'productType' => 'integer',
            'productName' => 'string',
        ]);
    }
}
