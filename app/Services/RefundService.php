<?php

namespace App\Services;

use App\Models\Refund;
use App\Utils\Inputs\RefundInput;
use App\Utils\Inputs\RefundPageInput;
use App\Utils\Inputs\StatusPageInput;

class RefundService extends BaseService
{
    public function createRefund($userId, RefundInput $input, $refundAmount)
    {
        $refund = Refund::new();
        $refund->user_id = $userId;
        $refund->shop_id = $input->shopId;
        $refund->order_id = $input->orderId;
        $refund->order_sn = $input->orderSn;
        $refund->coupon_id = $input->couponId ?? 0;
        $refund->order_goods_id = $input->orderGoodsId;
        $refund->goods_id = $input->goodsId;
        $refund->refund_address_id = $input->refundAddressId ?? 0;
        $refund->refund_amount = $refundAmount;
        return $this->updateRefund($refund, $input);
    }

    public function updateRefund(Refund $refund, RefundInput $input)
    {
        if ($refund->status == 2) {
            $refund->status = 0;
            $refund->failure_reason = '';
        }
        $refund->refund_type = $input->type;
        $refund->refund_reason = $input->reason;
        $refund->image_list = json_encode($input->imageList);
        $refund->save();

        return $refund;
    }

    public function getRefundList(StatusPageInput $input, $columns = ['*'])
    {
        $query = Refund::query();
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getShopTotal($shopId, $statusList)
    {
        return Refund::query()->where('shop_id', $shopId)->whereIn('status', $statusList)->count();
    }

    public function getShopRefundList($shopId, RefundPageInput $input, $columns = ['*'])
    {
        $query = Refund::query()->where('shop_id', $shopId);
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        if (!is_null($input->orderSn)) {
            $query = $query->where('order_sn', $input->orderSn);
        }
        return $query
            ->orderByRaw("FIELD(status, 0, 2) DESC")
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getRefundById($id, $columns = ['*'])
    {
        return Refund::query()->find($id, $columns);
    }

    public function getShopRefund($shopId, $id, $columns = ['*'])
    {
        return Refund::query()->where('shop_id', $shopId)->find($id, $columns);
    }

    public function getRefundByUserId($userId, $orderId, $goodsId, $columns = ['*'])
    {
        return Refund::query()
            ->where('user_id', $userId)
            ->where('order_id', $orderId)
            ->where('goods_id', $goodsId)
            ->first($columns);
    }

    public function getUserRefund($userId, $id, $columns = ['*'])
    {
        return Refund::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getListByIds(array $ids, $columns = ['*'])
    {
        return Refund::query()->whereIn('id', $ids)->get($columns);
    }

    public function getCountByStatus($status)
    {
        return Refund::query()->where('status', $status)->count();
    }

    public function getShopCountByStatus($shopId, $status)
    {
        return Refund::query()->where('shop_id', $shopId)->where('status', $status)->count();
    }
}
