<?php

namespace App\Utils\Inputs;

class AdminAddInput extends BaseInput
{
    public $avatar;
    public $nickname;
    public $account;
    public $password;
    public $roleId;

    public function rules()
    {
        return [
            'avatar' => 'string',
            'nickname' => 'string',
            'account' => 'required|string',
            'password' => 'required|string',
            'roleId' => 'required|integer|digits_between:1,20',
        ];
    }
}
