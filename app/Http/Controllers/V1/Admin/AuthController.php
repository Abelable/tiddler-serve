<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminService;
use App\Utils\CodeResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $guard = 'admin';
    protected $only = ['logout', 'info'];

    public function login()
    {
        $account = $this->verifyRequiredString('account');
        $password = $this->verifyRequiredString('password');

        $user = AdminService::getInstance()->getUserByAccount($account);
        if (is_null($user)) {
            return $this->fail(CodeResponse::INVALID_ACCOUNT);
        }

        $isPass = Hash::check($password, $user->getAuthPassword());
        if (!$isPass) {
            return $this->fail(CodeResponse::INVALID_ACCOUNT);
        }

        $token = Auth::guard('admin')->login($user);
        return $this->success($token);
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return $this->success();
    }

    public function info()
    {
        $user = $this->admin();
        return $this->success([
            'nickname' => $user->nickname,
            'avatar' => $user->avatar
        ]);
    }
}
