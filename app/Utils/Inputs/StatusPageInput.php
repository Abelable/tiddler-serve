<?php

namespace App\Utils\Inputs;

class StatusPageInput extends PageInput
{
    public $status;

    public function rules()
    {
        return array_merge([
            'status' => 'integer|in:0,1,2,3,4,5,6',
        ], parent::rules());
    }
}
