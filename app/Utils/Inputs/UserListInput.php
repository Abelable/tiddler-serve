<?php

namespace App\Utils\Inputs;

class UserListInput extends PageInput
{
    public $nickname;
    public $mobile;

    public function rules()
    {
        return array_merge([
            'nickname' => 'string',
            'mobile' => 'regex:/^1[345789][0-9]{9}$/',
        ], parent::rules());
    }
}
