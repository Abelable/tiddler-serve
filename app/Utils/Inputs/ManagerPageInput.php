<?php

namespace App\Utils\Inputs;

class ManagerPageInput extends PageInput
{
    public $nickname;
    public $mobile;
    public $roleId;
    public function rules()
    {
        return array_merge(parent::rules(), [
            'nickname' => 'string',
            'mobile' => 'regex:/^1[3-9]\d{9}$/',
            'roleId' => 'integer|digits_between:1,20',
        ]);
    }
}
