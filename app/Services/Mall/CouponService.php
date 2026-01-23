<?php

namespace App\Services\Mall;

use App\Jobs\CouponExpireJob;
use App\Models\Mall\Coupon;
use App\Models\Mall\Goods\Goods;
use App\Models\Mall\UserCoupon;
use App\Services\BaseService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CouponInput;
use App\Utils\Inputs\CouponPageInput;
use Illuminate\Support\Facades\DB;

class CouponService extends BaseService
{
    public function getCouponPage(CouponPageInput $input, $shopId = 0, $columns = ['*'])
    {
        $query = Coupon::query()->where('shop_id', $shopId);
        if (!is_null($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        if (!is_null($input->type)) {
            $query = $query->where('type', $input->type);
        }
        if (!is_null($input->goodsId)) {
            $query = $query->where('goods_id', $input->goodsId);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getCouponList($status, $columns = ['*'])
    {
        return Coupon::query()->where('status', $status)->get($columns);
    }

    public function getCouponListByGoodsIds(array $goodsIds, $columns = ['*'])
    {
        return Coupon::query()->where('status', 1)->whereIn('goods_id', $goodsIds)->get($columns);
    }

    public function getCouponListByGoodsId($goodsId, $columns = ['*'])
    {
        return Coupon::query()->where('status', 1)->where('goods_id', $goodsId)->get($columns);
    }

    public function getCouponById($id, $columns = ['*'])
    {
        return Coupon::query()->find($id, $columns);
    }

    public function getShopCoupon($shopId, $id, $columns = ['*'])
    {
        return Coupon::query()->where('shop_id', $shopId)->where('id', $id)->first($columns);
    }

    public function getGoodsCoupon($id, $goodsId, $columns = ['*'])
    {
        return Coupon::query()->where('goods_id', $goodsId)->where('id', $id)->first($columns);
    }

    public function getCouponListByIds(array $ids, $columns = ['*'])
    {
        return Coupon::query()->whereIn('id', $ids)->get($columns);
    }

    public function getAvailableCouponById($id, $columns = ['*'])
    {
        return Coupon::query()->where('status', 1)->where('id', $id)->first($columns);
    }

    public function getAvailableCouponListByIds(array $ids, $columns = ['*'])
    {
        return Coupon::query()->where('status', 1)->whereIn('id', $ids)->get($columns);
    }

    public function expireCoupon($id)
    {
        $coupon = $this->getCouponById($id);
        if (!is_null($coupon)) {
            $coupon->status = 2;
            $coupon->save();
        }
        return $coupon;
    }

    public function updateCoupon(Coupon $coupon, CouponInput $input, Goods $goods = null)
    {
        if ($coupon->status == 2) {
            $coupon->status = 1;
        }
        $coupon->denomination = $input->denomination;
        $coupon->name = $input->name;
        $coupon->description = $input->description;
        if (!is_null($goods)) {
            $coupon->goods_id = $goods->id;
            $coupon->goods_cover = $goods->cover;
            $coupon->goods_name = $goods->name;
        }
        $coupon->type = $input->type;
        $coupon->num_limit = $input->numLimit ?? 0;
        $coupon->price_limit = $input->priceLimit ?? 0;
        $coupon->receive_limit = $input->receiveLimit ?? 0;
        if (!is_null($input->expirationTime)) {
            $coupon->expiration_time = $input->expirationTime;
            dispatch(new CouponExpireJob($coupon->id, $input->expirationTime));
        }
        $coupon->save();
    }

    public function handelExpiredCoupons()
    {
        return Coupon::query()
            ->where('status', 1)
            ->where('expiration_time', '<=', date('Y-m-d H:i:s', time()))
            ->update(['status' => 2]);
    }

    public function receiveCoupon($userId, $id)
    {
        $coupon = $this->getAvailableCouponById($id);
        if (is_null($coupon)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '优惠券不存在');
        }

        $receivedCount = UserCouponService::getInstance()->getReceivedCount($userId, $id);
        if ($coupon->receive_limit != 0 && $receivedCount >= $coupon->receive_limit) {
            $this->throwBusinessException(CodeResponse::INVALID_OPERATION, '已超优惠券限制数量，请勿重复领取');
        }

        DB::transaction(function () use ($userId, $coupon) {
            $userCoupon = UserCoupon::new();
            $userCoupon->user_id = $userId;
            $userCoupon->coupon_id = $coupon->id;
            $userCoupon->save();

            $coupon->received_num = $coupon->received_num + 1;
            $coupon->save();
        });
    }
}
