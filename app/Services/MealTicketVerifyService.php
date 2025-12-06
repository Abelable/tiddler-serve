<?php

namespace App\Services;

use App\Models\Catering\MealTicketVerifyCode;

class MealTicketVerifyService extends BaseService
{
    public function createVerifyCode($orderId, $shopId, $expirationTime = null)
    {
        $verify = MealTicketVerifyCode::new();
        $verify->order_id = $orderId;
        $verify->shop_id = $shopId;
        $verify->code = MealTicketVerifyCode::generateVerifyCode();
        if ($expirationTime) {
            $verify->expiration_time = $expirationTime;
        }
        $verify->save();
        return $verify;
    }

    public function getVerifyCodeInfo($orderId, $restaurantId,  $columns = ['*'])
    {
        return MealTicketVerifyCode::query()
            ->where('order_id', $orderId)
            ->where('restaurant_id', $restaurantId)
            ->first($columns);
    }

    public function getByCode($code, $columns = ['*'])
    {
        return MealTicketVerifyCode::query()
            ->where('code', $code)
            ->where('status', 0)
            ->first($columns);
    }

    public function getById($id, $columns = ['*'])
    {
        return MealTicketVerifyCode::query()->find($id, $columns);
    }

    public function getPendingListByOrderId($orderId, $columns = ['*'])
    {
        return MealTicketVerifyCode::query()
            ->where('order_id', $orderId)
            ->where('status', 0)
            ->get($columns);
    }

    public function hasUnverifiedCodes($orderId)
    {
        return MealTicketVerifyCode::query()
            ->where('order_id', $orderId)
            ->where('status', 0)
            ->exists();
    }

    public function verify(MealTicketVerifyCode $verifyCodeInfo, $userId)
    {
        $verifyCodeInfo->status = 1;
        $verifyCodeInfo->verifier_id = $userId;
        $verifyCodeInfo->verify_time = now();
        $verifyCodeInfo->save();
        return $verifyCodeInfo;
    }
}
