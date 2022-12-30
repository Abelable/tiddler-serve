<?php

namespace App\Utils\Inputs;

class MerchantListInput extends PageInput
{
    public $type;
    public $name;
    public $mobile;

    public function rules()
    {
        return array_merge([
            'type' => 'integer',
            'name' => 'string',
            'mobile' => 'regex:/^1[345789][0-9]{9}$/',
        ], parent::rules());
    }
}
