<?php

namespace App\Services;

use App\Models\Catering\MealTicketVerifyCode;
use App\Models\Catering\MealTicketVerifyLog;

class MealTicketVerifyService extends BaseService
{
    public function createVerifyCode($orderId, $restaurantId, $expirationTime = '')
    {
        $verify = MealTicketVerifyCode::new();
        $verify->order_id = $orderId;
        $verify->restaurant_id = $restaurantId;
        $verify->code = MealTicketVerifyCode::generateVerifyCode();
        $verify->expiration_time = $expirationTime;
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
        $verifyCodeInfo->save();

        $log = MealTicketVerifyLog::new();
        $log->order_id = $verifyCodeInfo->order_id;
        $log->restaurant_id = $verifyCodeInfo->restaurant_id;
        $log->verifier_id = $userId;
        $log->verify_time = now();
        $log->save();
    }
}
