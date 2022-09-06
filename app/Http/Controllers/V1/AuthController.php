<?php

namespace App\Http\Controllers\V1;

use App\Utils\WxMpServe;

class AuthController extends BaseController
{
    protected $only = [];

    public function getWxMpUserMobile()
    {
        $code = $this->verifyRequiredString('code');
        $mobile = WxMpServe::new()->getUserPhoneNumber($code);
        return $this->success($mobile);
    }
}
