<?php

namespace App\Utils\Inputs;

class ManagerInput extends BaseInput
{
    public $shopId;
    public $userId;
    public $avatar;
    public $nickname;
    public $roleId;

    public function rules()
    {
        return [
            'shopId' => 'required|integer|digits_between:1,20',
            'userId' => 'required|integer|digits_between:1,20',
            'avatar' => 'string',
            'nickname' => 'string',
            'roleId' => 'required|integer|digits_between:1,20',
        ];
    }
}
