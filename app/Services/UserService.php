<?php

namespace App\Services;

use App\Models\User;
use App\Utils\Inputs\WxMpRegisterInput;

class UserService extends BaseService
{
    public function register($openid, $unionid, WxMpRegisterInput $input)
    {
        $user = User::new();
        $user->openid = $openid;
        $user->unionid = $unionid;
        $user->avatar = $input->avatar;
        $user->nickname = $input->nickname;
        $user->gender = $input->gender;
        $user->mobile = $input->mobile;
        $user->save();
        return $user;
    }

    public function getByOpenid($openid)
    {
        return User::query()->where('openid', $openid)->first();
    }
}
