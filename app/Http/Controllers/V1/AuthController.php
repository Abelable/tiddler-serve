<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Models\UserOpenId;
use App\Services\AccountService;
use App\Services\Activity\NewYearTaskService;
use App\Services\Promoter\PromoterChangeLogService;
use App\Services\Promoter\PromoterService;
use App\Services\RelationService;
use App\Services\UserOpenIdService;
use App\Services\UserService;
use App\Services\WxMpService;
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

        $appId = request()->header('appId');
        if (empty($appId)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, 'appId不能为空');
        }

        $user = UserService::getInstance()->getByMobile($input->mobile);
        if (!is_null($user)) {
            return $this->fail(CodeResponse::AUTH_NAME_REGISTERED);
        }

        $secret = WxMpService::getInstance()->getSecret($appId);
        $session = WxMpServe::new()->getUserSession($input->code, $appId, $secret);

        $token = DB::transaction(function () use ($appId, $input, $session) {
            // 用户注册
            $user = UserService::getInstance()->register($input, $session['unionid'] ?? null);
            UserOpenIdService::getInstance()->create($user->id, $session['openid'], $appId);

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
            $promoter = PromoterService::getInstance()->adminCreate($user->id, 1, 100, 100);
            PromoterChangeLogService::getInstance()->createLog($promoter->id, 1);
            if (!empty($input->superiorId)) {
                PromoterService::getInstance()->updateSubPromoterCount($input->superiorId);
            }

            return Auth::guard('user')->login($user);
        });

        return $this->success($token);
    }

    public function wxMpLogin()
    {
        $code = $this->verifyRequiredString('code');

        $appId = request()->header('appId');
        if (empty($appId)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, 'appId不能为空');
        }

        $secret = WxMpService::getInstance()->getSecret($appId);
        $session = WxMpServe::new()->getUserSession($code, $appId, $secret);
        $unionid = $session['unionid'] ?? null;
        $openid  = $session['openid'];

        $user = DB::transaction(function () use ($appId, $unionid, $openid) {
            $user = null;
            if (!empty($unionid)) {
                $user = UserService::getInstance()->getByUnionid($unionid);
                if ($user) {
                    UserOpenIdService::getInstance()->create($user->id, $openid, $appId);
                }
            }

            if (!$user) {
                /** @var UserOpenId $userOpenId */
                $userOpenId = UserOpenIdService::getInstance()->getByOpenId($openid, $appId);
                if ($userOpenId) {
                    $user = UserService::getInstance()->getUserById($userOpenId->user_id);
                    if ($user && empty($user->unionid) && !empty($unionid)) {
                        $user->unionid = $unionid;
                        $user->save();
                    }
                }
            }

            return $user;
        });



        $token = '';
        if ($user) {
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
