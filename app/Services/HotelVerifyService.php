<?php

namespace App\Services;

use App\Models\HotelVerifyCode;

class HotelVerifyService extends BaseService
{
    public function createVerifyCode($orderId, $hotelId, $expirationTime = null)
    {
        $verify = HotelVerifyCode::new();
        $verify->order_id = $orderId;
        $verify->hotel_id = $hotelId;
        $verify->code = HotelVerifyCode::generateVerifyCode();
        if ($expirationTime) {
            $verify->expiration_time = $expirationTime;
        }
        $verify->save();
        return $verify;
    }

    public function getVerifyCodeInfo($orderId, $hotelId,  $columns = ['*'])
    {
        return HotelVerifyCode::query()->where('order_id', $orderId)->where('hotel_id', $hotelId)->first($columns);
    }

    public function getByCode($code, $columns = ['*'])
    {
        return HotelVerifyCode::query()->where('code', $code)->where('status', 0)->first($columns);
    }

    public function getById($id, $columns = ['*'])
    {
        return HotelVerifyCode::query()->find($id, $columns);
    }

    public function getPendingListByOrderId($orderId, $columns = ['*'])
    {
        return HotelVerifyCode::query()->where('order_id', $orderId)->where('status', 0)->get($columns);
    }

    public function verify(HotelVerifyCode $verifyCodeInfo, $userId)
    {
        $verifyCodeInfo->status = 1;
        $verifyCodeInfo->verifier_id = $userId;
        $verifyCodeInfo->verify_time = now();
        $verifyCodeInfo->save();
        return $verifyCodeInfo;
    }
}
