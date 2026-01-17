<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Services\AccountService;
use App\Services\Activity\NewYearTaskService;
use App\Services\Promoter\PromoterChangeLogService;
use App\Services\Promoter\PromoterService;
use App\Services\RelationService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\WxMpRegisterInput;
use App\Utils\WxMpServe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $only = ['setPassword', 'resetPassword'];

    public function getWxMpUserMobile()
    {
        $code = $this->verifyRequiredString('code');
        $mobile = WxMpServe::new()->getUserPhoneNumber($code);
        return $this->success($mobile);
    }

    public function wxMpRegister()
    {
        /** @var WxMpRegisterInput $input */
        $input = WxMpRegisterInput::new();

        $result = WxMpServe::new()->getUserOpenid($input->code);
        $user = UserService::getInstance()->getByMobile($input->mobile);
        if (!is_null($user)) {
            return $this->fail(CodeResponse::AUTH_NAME_REGISTERED);
        }

        $token = DB::transaction(function () use ($input, $result) {
            // 用户注册
            $user = UserService::getInstance()->register($result['openid'], $input);


            if (!empty($input->superiorId)) {
                // 绑定上下级
                RelationService::getInstance()->banding($input->superiorId, $user->id);

                // 增加上级邀请用户数量
                PromoterService::getInstance()->updateSubUserCount($input->superiorId);

                // todo 团圆家乡年 - 邀请任务
                NewYearTaskService::getInstance()->finishInviteTask($input->superiorId);
            }

            // 创建用户余额账户
            AccountService::getInstance()->createUserAccount($user->id);

            // todo 注册送身份（100天代言人）
//            $promoter = PromoterService::getInstance()->adminCreate($user->id, 1, 100, 100);
//            PromoterChangeLogService::getInstance()->createLog($promoter->id, 1);
//            if (!empty($input->superiorId)) {
//                PromoterService::getInstance()->updateSubPromoterCount($input->superiorId);
//            }

            return Auth::guard('user')->login($user);
        });

        return $this->success($token);
    }

    public function wxMpLogin()
    {
        $code = $this->verifyRequiredString('code');
        $result = WxMpServe::new()->getUserOpenid($code);
        $user = UserService::getInstance()->getByOpenid($result['openid']);
        $token = '';
        if (!is_null($user)) {
            $token = Auth::guard('user')->login($user);
        }
        return $this->success($token);
    }

    public function login()
    {
        $mobile = $this->verifyRequiredString('mobile');
        $password = $this->verifyRequiredString('password');

        $user = UserService::getInstance()->getByMobile($mobile);
        if (is_null($user)) {
            return $this->fail(CodeResponse::INVALID_ACCOUNT);
        }

        $isPass = Hash::check($password, $user->getAuthPassword());
        if (!$isPass) {
            return $this->fail(CodeResponse::INVALID_ACCOUNT);
        }

        $token = Auth::guard('user')->login($user);

        $scenicShopOptions = UserService::getInstance()->scenicShopOptions($user);
        $hotelShopOptions = UserService::getInstance()->hotelShopOptions($user);
        $cateringShopOptions = UserService::getInstance()->cateringShopOptions($user);
        $goodsShopOptions = UserService::getInstance()->shopOptions($user);

        return $this->success([
            'token' => $token,
            'scenicShopOptions' => $scenicShopOptions,
            'hotelShopOptions' => $hotelShopOptions,
            'cateringShopOptions' => $cateringShopOptions,
            'goodsShopOptions' => $goodsShopOptions,
        ]);
    }

    public function refreshToken() {
        try {
            $token = Auth::guard('user')->refresh();

            // todo 由于删除用户之后，鉴权失败，但刷新token依旧有效，暂未找到解决办法，因此增加这一层校验
            try {
                Auth::guard('user')->userOrFail();
            } catch (\Exception $e) {
                throw new BusinessException(CodeResponse::FORBIDDEN, 'token失效，请重新登录');
            }
        } catch (\Exception $e) {
            throw new BusinessException(CodeResponse::FORBIDDEN, 'token失效，请重新登录');
        }
        return $this->success($token);
    }

    public function setPassword()
    {
        $password = $this->verifyRequiredString('password');

        $user = $this->user();
        $user->password = Hash::make($password);
        $user->save();

        return $this->success();
    }

    public function resetPassword()
    {
        $password = $this->verifyRequiredString('password');
        $newPassword = $this->verifyRequiredString('newPassword');
        $user = $this->user();

        $isPass = Hash::check($password, $user->getAuthPassword());
        if (!$isPass) {
            return $this->fail(CodeResponse::INVALID_ACCOUNT, '原密码错误');
        }
        $user->password = Hash::make($newPassword);
        $user->save();

        return $this->success();
    }
}
