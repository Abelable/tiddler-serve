<?php

namespace App\Services\Mall\Goods;

use App\Models\Mall\Goods\GoodsVerifyCode;
use App\Services\BaseService;

class GoodsVerifyService extends BaseService
{
    public function createVerifyCode($orderId, $shopId, $expirationTime = null)
    {
        $verify = GoodsVerifyCode::new();
        $verify->order_id = $orderId;
        $verify->shop_id = $shopId;
        $verify->code = GoodsVerifyCode::generateVerifyCode();
        if ($expirationTime) {
            $verify->expiration_time = $expirationTime;
        }
        $verify->save();
        return $verify;
    }

    public function getByCode($code, $columns = ['*'])
    {
        return GoodsVerifyCode::query()
            ->where('code', $code)
            ->where('status', 0)
            ->first($columns);
    }

    public function getByOrderId($orderId, $columns = ['*'])
    {
        return GoodsVerifyCode::query()->where('order_id', $orderId)->first($columns);
    }

    public function getById($id, $columns = ['*'])
    {
        return GoodsVerifyCode::query()->find($id, $columns);
    }

    public function verify(GoodsVerifyCode $verifyCodeInfo, $userId)
    {
        $verifyCodeInfo->status = 1;
        $verifyCodeInfo->verifier_id = $userId;
        $verifyCodeInfo->verify_time = now();
        $verifyCodeInfo->save();
        return $verifyCodeInfo;
    }
}
