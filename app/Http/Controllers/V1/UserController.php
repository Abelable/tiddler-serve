<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;

class UserController extends Controller
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
