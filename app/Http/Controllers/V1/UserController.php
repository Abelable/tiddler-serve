<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Utils\TimServe;

class UserController extends Controller
{
    public function getUserInfo()
    {
        $user = $this->user();
        return $this->success([
            'avatar' => $user->avatar,
            'nickname' => $user->nickname,
            'gender' => $user->gender,
            'mobile' => $user->mobile,
            'shopId' => $user->shop_id,
        ]);
    }

    public function getTimLoginInfo()
    {
        $timServe = TimServe::new();
        $timServe->updateUserInfo($this->userId(), $this->user()->nickname, $this->user()->avatar);
        $loginInfo = $timServe->getLoginInfo($this->user());
        return $this->success($loginInfo);
    }
}
