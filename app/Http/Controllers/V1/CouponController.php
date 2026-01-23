<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Mall\Coupon;
use App\Models\Mall\UserCoupon;
use App\Services\Mall\CouponService;
use App\Services\Mall\UserCouponService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
    public function receiveCoupon()
    {
        $id = $this->verifyRequiredId('id');
        CouponService::getInstance()->receiveCoupon($this->userId(), $id);
        return $this->success();
    }

    public function userCouponList()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();

        $page = UserCouponService::getInstance()->getUserCouponPage($this->userId(), $input);
        $userCouponList = collect($page->items());

        $couponIds = $userCouponList->pluck('coupon_id')->toArray();
        $couponList = CouponService::getInstance()->getCouponListByIds($couponIds)->keyBy('id');

        $list = $userCouponList->map(function (UserCoupon $userCoupon) use ($couponList) {
            /** @var Coupon $coupon */
            $coupon = $couponList->get($userCoupon->coupon_id);
            if (is_null($coupon)) {
                return null;
            }
            // 处理优惠券已过期、已下架情况下，用户领取优惠券状态问题
            if ($coupon->status != 1 && $userCoupon->status == 1) {
                $userCoupon->status = 3;
                $userCoupon->save();
                return null;
            }
            $coupon->status = $userCoupon->status;
            return $coupon;
        })->filter(function ($coupon) {
            return !is_null($coupon);
        })->values();

        return $this->success($this->paginate($page, $list));
    }
}
