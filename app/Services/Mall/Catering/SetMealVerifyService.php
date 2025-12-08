<?php

namespace App\Services\Mall\Catering;

use App\Models\Mall\Catering\SetMealVerifyCode;
use App\Services\BaseService;

class SetMealVerifyService extends BaseService
{
    public function createVerifyCode($orderId, $shopId, $expirationTime = null)
    {
        $verify = SetMealVerifyCode::new();
        $verify->order_id = $orderId;
        $verify->shop_id = $shopId;
        $verify->code = SetMealVerifyCode::generateVerifyCode();
        if ($expirationTime) {
            $verify->expiration_time = $expirationTime;
        }
        $verify->save();
        return $verify;
    }

    public function getVerifyCodeInfo($orderId, $restaurantId,  $columns = ['*'])
    {
        return SetMealVerifyCode::query()->where('order_id', $orderId)->where('restaurant_id', $restaurantId)->first($columns);
    }

    public function getByCode($code, $columns = ['*'])
    {
        return SetMealVerifyCode::query()->where('code', $code)->where('status', 0)->first($columns);
    }

    public function getById($id, $columns = ['*'])
    {
        return SetMealVerifyCode::query()->find($id, $columns);
    }

    public function getPendingListByOrderId($orderId, $columns = ['*'])
    {
        return SetMealVerifyCode::query()->where('order_id', $orderId)->where('status', 0)->get($columns);
    }

    public function hasUnverifiedCodes($orderId)
    {
        return SetMealVerifyCode::query()
            ->where('order_id', $orderId)
            ->where('status', 0)
            ->exists();
    }

    public function verify(SetMealVerifyCode $verifyCodeInfo, $userId)
    {
        $verifyCodeInfo->status = 1;
        $verifyCodeInfo->verifier_id = $userId;
        $verifyCodeInfo->verify_time = now();
        $verifyCodeInfo->save();
        return $verifyCodeInfo;
    }
}
