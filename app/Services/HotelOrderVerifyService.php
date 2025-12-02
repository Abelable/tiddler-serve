<?php

namespace App\Services;

use App\Models\HotelOrderVerifyCode;
use App\Models\HotelOrderVerifyLog;

class HotelOrderVerifyService extends BaseService
{
    public function createVerifyCode($orderId, $hotelId, $expirationTime = '')
    {
        $verify = HotelOrderVerifyCode::new();
        $verify->order_id = $orderId;
        $verify->hotel_id = $hotelId;
        $verify->code = HotelOrderVerifyCode::generateVerifyCode();
        $verify->expiration_time = $expirationTime;
        $verify->save();
        return $verify;
    }

    public function getVerifyCodeInfo($orderId, $hotelId,  $columns = ['*'])
    {
        return HotelOrderVerifyCode::query()->where('order_id', $orderId)->where('hotel_id', $hotelId)->first($columns);
    }

    public function getByCode($code, $columns = ['*'])
    {
        return HotelOrderVerifyCode::query()->where('code', $code)->where('status', 0)->first($columns);
    }

    public function getById($id, $columns = ['*'])
    {
        return HotelOrderVerifyCode::query()->find($id, $columns);
    }

    public function getPendingListByOrderId($orderId, $columns = ['*'])
    {
        return HotelOrderVerifyCode::query()->where('order_id', $orderId)->where('status', 0)->get($columns);
    }

    public function verify(HotelOrderVerifyCode $verifyCodeInfo, $userId)
    {
        $verifyCodeInfo->status = 1;
        $verifyCodeInfo->save();

        $log = HotelOrderVerifyLog::new();
        $log->order_id = $verifyCodeInfo->order_id;
        $log->hotel_id = $verifyCodeInfo->hotel_id;
        $log->verifier_id = $userId;
        $log->verify_time = now();
        $log->save();
    }
}
