<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Models\Catering\CateringShop;
use App\Models\Catering\CateringShopManager;
use App\Models\HotelShop;
use App\Models\HotelShopManager;
use App\Models\ScenicShop;
use App\Models\ScenicShopManager;
use App\Models\Shop;
use App\Models\ShopManager;
use App\Models\User;
use App\Services\AccountService;
use App\Services\HotelShopManagerService;
use App\Services\HotelShopService;
use App\Services\Mall\Catering\CateringShopManagerService;
use App\Services\Mall\Catering\CateringShopService;
use App\Services\PromoterService;
use App\Services\RelationService;
use App\Services\ScenicShopManagerService;
use App\Services\ScenicShopService;
use App\Services\ShopManagerService;
use App\Services\ShopService;
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
            }

            // 创建用户余额
            AccountService::getInstance()->createUserAccount($user->id);

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

        $scenicShopOptions = $this->scenicShopOptions($user);
        $hotelShopOptions = $this->hotelShopOptions($user);
        $cateringShopOptions = $this->cateringShopOptions($user);
        $shopOptions = $this->shopOptions($user);

        return $this->success([
            'token' => $token,
            'scenicShopOptions' => $scenicShopOptions,
            'hotelShopOptions' => $hotelShopOptions,
            'cateringShopOptions' => $cateringShopOptions,
            'shopOptions' => $shopOptions,
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

    private function scenicShopOptions(User $user) {
        $scenicShopId = $user->scenicShop->id ?? 0;

        $scenicShopManagerList = ScenicShopManagerService::getInstance()->getManagerListByRoleIds($user->id, [1, 2]);
        $shopIds = $scenicShopManagerList->pluck('shop_id')->toArray();

        if ($scenicShopId != 0 && !in_array($scenicShopId, $shopIds)) {
            $shopIds[] = $scenicShopId;
        }

        if (empty($shopIds)) {
            return collect([]);
        }

        $scenicShopList = ScenicShopService::getInstance()->getShopListByIds($shopIds)->keyBy('id');

        $scenicShopOptions = $scenicShopManagerList->map(function (ScenicShopManager $scenicShopManager) use ($scenicShopList) {
            /** @var ScenicShop $shopInfo */
            $shopInfo = $scenicShopList->get($scenicShopManager->shop_id);
            return [
                'id' => $scenicShopManager->shop_id,
                'logo' => $shopInfo->logo ?? '',
                'name' => $shopInfo->name ?? '',
                'roleId' => $scenicShopManager->role_id,
            ];
        });

        if ($scenicShopId != 0) {
            $shopInfo = $scenicShopList->get($scenicShopId);
            $scenicShopOptions->push([
                'id' => $scenicShopId,
                'logo' => $shopInfo->logo ?? '',
                'name' => $shopInfo->name ?? '',
                'roleId' => 1,
            ]);
        }

        return $scenicShopOptions;
    }

    private function hotelShopOptions(User $user) {
        $hotelShopId = $user->hotelShop->id ?? 0;

        $hotelShopManagerList = HotelShopManagerService::getInstance()->getManagerListByRoleIds($user->id, [1, 2]);
        $shopIds = $hotelShopManagerList->pluck('shop_id')->toArray();

        if ($hotelShopId != 0 && !in_array($hotelShopId, $shopIds)) {
            $shopIds[] = $hotelShopId;
        }

        if (empty($shopIds)) {
            return collect([]);
        }

        $hotelShopList = HotelShopService::getInstance()->getShopListByIds($shopIds)->keyBy('id');

        $hotelShopOptions = $hotelShopManagerList->map(function (HotelShopManager $manager) use ($hotelShopList) {
            /** @var HotelShop $shopInfo */
            $shopInfo = $hotelShopList->get($manager->shop_id);
            return [
                'id' => $manager->shop_id,
                'logo' => $shopInfo->logo ?? '',
                'name' => $shopInfo->name ?? '',
                'roleId' => $manager->role_id,
            ];
        });

        if ($hotelShopId != 0) {
            $shopInfo = $hotelShopList->get($hotelShopId);
            $hotelShopOptions->push([
                'id' => $hotelShopId,
                'logo' => $shopInfo->logo ?? '',
                'name' => $shopInfo->name ?? '',
                'roleId' => 1,
            ]);
        }

        return $hotelShopOptions;
    }

    private function cateringShopOptions(User $user) {
        $cateringShopId = $user->cateringShop->id ?? 0;

        $cateringShopManagerList = CateringShopManagerService::getInstance()->getManagerListByRoleIds($user->id, [1, 2]);
        $shopIds = $cateringShopManagerList->pluck('shop_id')->toArray();

        if ($cateringShopId != 0 && !in_array($cateringShopId, $shopIds)) {
            $shopIds[] = $cateringShopId;
        }

        if (empty($shopIds)) {
            return collect([]);
        }

        $cateringShopList = CateringShopService::getInstance()->getShopListByIds($shopIds)->keyBy('id');

        $cateringShopOptions = $cateringShopManagerList->map(function (CateringShopManager $manager) use ($cateringShopList) {
            /** @var CateringShop $shopInfo */
            $shopInfo = $cateringShopList->get($manager->shop_id);
            return [
                'id' => $manager->shop_id,
                'logo' => $shopInfo->logo ?? '',
                'name' => $shopInfo->name ?? '',
                'roleId' => $manager->role_id,
            ];
        });

        if ($cateringShopId != 0) {
            $shopInfo = $cateringShopList->get($cateringShopId);
            $cateringShopOptions->push([
                'id' => $cateringShopId,
                'logo' => $shopInfo->logo ?? '',
                'name' => $shopInfo->name ?? '',
                'roleId' => 1,
            ]);
        }

        return $cateringShopOptions;
    }

    private function shopOptions(User $user) {
        $shopId = $user->shop->id ?? 0;

        $shopManagerList = ShopManagerService::getInstance()->getManagerListByRoleIds($user->id, [1, 2]);
        $shopIds = $shopManagerList->pluck('shop_id')->toArray();

        if ($shopId != 0 && !in_array($shopId, $shopIds)) {
            $shopIds[] = $shopId;
        }

        if (empty($shopIds)) {
            return collect([]);
        }

        $shopList = ShopService::getInstance()->getShopListByIds($shopIds)->keyBy('id');

        $shopOptions = $shopManagerList->map(function (ShopManager $manager) use ($shopList) {
            /** @var Shop $shopInfo */
            $shopInfo = $shopList->get($manager->shop_id);
            return [
                'id' => $manager->shop_id,
                'logo'   => $shopInfo->logo ?? '',
                'name'   => $shopInfo->name ?? '',
                'roleId' => $manager->role_id,
            ];
        });

        if ($shopId != 0) {
            $shopInfo = $shopList->get($shopId);
            $shopOptions->push([
                'id' => $shopId,
                'logo'   => $shopInfo->logo ?? '',
                'name'   => $shopInfo->name ?? '',
                'roleId' => 1,
            ]);
        }

        return $shopOptions;
    }
}
