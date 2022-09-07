<?php

namespace App\Http\Controllers\V1;

class UserController extends BaseController
{
    public function getUserInfo()
    {
        $user = $this->user();
        return $this->success([
            'avatar' => $user->avatar,
            'nickname' => $user->nickname,
            'gender' => $user->gender,
            'mobile' => $user->mobile
        ]);
    }
}
