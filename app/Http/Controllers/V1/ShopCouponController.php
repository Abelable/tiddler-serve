<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Mall\Coupon;
use App\Services\Mall\CouponService;
use App\Services\Mall\Goods\GoodsService;
use App\Services\Mall\UserCouponService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CouponInput;
use App\Utils\Inputs\CouponPageInput;
use Illuminate\Support\Facades\DB;

class ShopCouponController extends Controller
{
    public function list()
    {
        /** @var CouponPageInput $input */
        $input = CouponPageInput::new();
        $shopId = $this->verifyRequiredId('shopId');
        $list = CouponService::getInstance()->getCouponPage($input, $shopId);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $coupon = CouponService::getInstance()->getShopCoupon($shopId, $id);
        if (is_null($coupon)) {
            return $this->fail(CodeResponse::NOT_FOUND, '优惠券不存在');
        }

        return $this->success($coupon);
    }

    public function add()
    {
        /** @var CouponInput $input */
        $input = CouponInput::new();

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($input->goodsIds, ['id', 'cover', 'name']);
        foreach ($goodsList as $goods) {
            $coupon = Coupon::new();
            $coupon->shop_id = $input->shopId;
            CouponService::getInstance()->updateCoupon($coupon, $input, $goods);
        }

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var CouponInput $input */
        $input = CouponInput::new();

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($input->goodsIds, ['id', 'cover', 'name'])->keyBy('id');
        foreach ($input->goodsIds as $goodsId) {
            $coupon = CouponService::getInstance()->getGoodsCoupon($id, $goodsId);
            if (is_null($coupon)) {
                $coupon = Coupon::new();
            }
            CouponService::getInstance()->updateCoupon($coupon, $input, $goodsList->get($goodsId));
        }

        return $this->success();
    }

    public function up()
    {
        $id = $this->verifyRequiredId('id');
        $coupon = CouponService::getInstance()->getCouponById($id);
        if (is_null($coupon)) {
            return $this->fail(CodeResponse::NOT_FOUND, '优惠券不存在');
        }
        $coupon->status = 1;
        $coupon->save();
        return $this->success();
    }

    public function down()
    {
        $id = $this->verifyRequiredId('id');
        $coupon = CouponService::getInstance()->getCouponById($id);
        if (is_null($coupon)) {
            return $this->fail(CodeResponse::NOT_FOUND, '优惠券不存在');
        }
        $coupon->status = 3;
        $coupon->save();
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $coupon = CouponService::getInstance()->getCouponById($id);
        if (is_null($coupon)) {
            return $this->fail(CodeResponse::NOT_FOUND, '优惠券不存在');
        }

        DB::transaction(function () use ($coupon) {
            $coupon->delete();
            UserCouponService::getInstance()->deleteByCouponId($coupon->id);
        });

        return $this->success();
    }
}
