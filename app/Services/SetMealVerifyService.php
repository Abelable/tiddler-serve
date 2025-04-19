<?php

namespace App\Services;

use App\Models\SetMealVerifyCode;
use App\Models\SetMealVerifyLog;

class SetMealVerifyService extends BaseService
{
    public function createVerifyCode($orderId, $restaurantId, $expirationTime = '')
    {
        $verify = SetMealVerifyCode::new();
        $verify->order_id = $orderId;
        $verify->restaurant_id = $restaurantId;
        $verify->code = SetMealVerifyCode::generateVerifyCode();
        $verify->expiration_time = $expirationTime;
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
        $verifyCodeInfo->save();

        $log = SetMealVerifyLog::new();
        $log->order_id = $verifyCodeInfo->order_id;
        $log->restaurant_id = $verifyCodeInfo->restaurant_id;
        $log->verifier_id = $userId;
        $log->verify_time = now()->toDateTimeString();
        $log->save();
    }
}
