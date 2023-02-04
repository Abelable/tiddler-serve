<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class AdminListInput extends PageInput
{
    public $nickname;
    public $account;
    public $roleId;

    public function rules()
    {
        return array_merge([
            'nickname' => 'string',
            'account' => 'string',
            'roleId' => 'integer|digits_between:1,20',
        ], parent::rules());
    }
}
