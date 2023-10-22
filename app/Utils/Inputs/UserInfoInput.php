<?php

namespace App\Utils\Inputs;

class UserInfoInput extends BaseInput
{
    public $bg;
    public $avatar;
    public $nickname;
    public $gender;
    public $birthday;
    public $constellation;
    public $career;
    public $signature;

    public function rules()
    {
        return [
            'bg' => 'string',
            'avatar' => 'string',
            'nickname' => 'string',
            'gender' => 'integer|in:0,1,2',
            'birthday' => 'string',
            'constellation' => 'string',
            'career' => 'string',
            'signature' => 'string',
        ];
    }
}
