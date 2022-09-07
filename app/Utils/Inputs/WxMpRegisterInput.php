<?php

namespace App\Utils\Inputs;

class WxMpRegisterInput extends Input
{
    public $code;
    public $avatar;
    public $nickname;
    public $gender;
    public $mobile;

    public function rules()
    {
        return [
            'code' => 'required|string',
            'avatar' => 'required|string',
            'nickname' => 'required|string',
            'gender' => 'integer|in:0,1,2',
            'mobile' => 'required|regex:/^1[345789][0-9]{9}$/',
        ];
    }
}
