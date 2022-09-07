<?php

namespace App\Http\Controllers\V1;

use App\Services\UserService;
use App\Utils\Inputs\WxMpRegisterInput;
use App\Utils\WxMpServe;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    protected $only = [];

    public function getWxMpUserMobile()
    {
        $code = $this->verifyRequiredString('code');
        $mobile = WxMpServe::new()->getUserPhoneNumber($code);
        return $this->success($mobile);
    }

    public function wxMpRegister()
    {
        $input = WxMpRegisterInput::new();
        $result = WxMpServe::new()->getUserOpenid($input->code);
        $user = UserService::getInstance()->register($result['openid'], $result['unionid'], $input);
        $token = Auth::guard('api')->login($user);
        return $this->success($token);
    }
}
