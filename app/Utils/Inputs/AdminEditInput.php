<?php

namespace App\Utils\Inputs;

class AdminEditInput extends BaseInput
{
    public $id;
    public $avatar;
    public $nickname;
    public $roleId;

    public function rules()
    {
        return [
            'id' => 'required|integer|digits_between:1,20',
            'avatar' => 'string',
            'nickname' => 'string',
            'roleId' => 'required|integer|digits_between:1,20',
        ];
    }
}
