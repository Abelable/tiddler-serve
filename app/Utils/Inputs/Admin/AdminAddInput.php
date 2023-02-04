<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

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
