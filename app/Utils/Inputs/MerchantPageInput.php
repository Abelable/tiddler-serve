<?php

namespace App\Utils\Inputs;

class MerchantPageInput extends PageInput
{
    public $type;
    public $status;
    public $name;
    public $mobile;

    public function rules()
    {
        return array_merge([
            'type' => 'integer|in:1,2',
            'status' => 'integer|in:0,1,2,3',
            'name' => 'string',
            'mobile' => 'regex:/^1[345789][0-9]{9}$/',
        ], parent::rules());
    }
}
