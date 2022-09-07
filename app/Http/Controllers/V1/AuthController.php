<?php

namespace App\Http\Controllers\V1;

use App\Services\UserService;
use App\Utils\CodeResponse;
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
        $user = UserService::getInstance()->getByOpenid($result['openid']);
        if (!is_null($user)) {
            return $this->fail(CodeResponse::AUTH_NAME_REGISTERED);
        }

        $user = UserService::getInstance()->register($result['openid'], $result['unionid'], $input);
        $token = Auth::guard('api')->login($user);
        return $this->success($token);
    }

    public function getUserInfoByCode()
    {
        $code = $this->verifyRequiredString('code');
        $result = WxMpServe::new()->getUserOpenid($code);
        $user = UserService::getInstance()->getByOpenid($result['openid']);
        $response = null;
        if (!is_null($user)) {
            $token = Auth::guard('api')->login($user);
            $response = [
                'token' => $token,
                'userInfo' => [
                    'avatar' => $user->avatar,
                    'nickname' => $user->nickname,
                    'gender' => $user->gender,
                    'mobile' => $user->mobile
                ]
            ];
        }
        return $this->success($response);
    }
}
