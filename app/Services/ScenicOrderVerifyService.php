<?php

namespace App\Services;

use App\Models\ScenicOrderVerifyCode;
use App\Models\ScenicOrderVerifyLog;

class ScenicOrderVerifyService extends BaseService
{
    public function createVerifyCode($orderId, $scenicId, $expirationTime = '')
    {
        $verify = ScenicOrderVerifyCode::new();
        $verify->order_id = $orderId;
        $verify->scenic_id = $scenicId;
        $verify->code = ScenicOrderVerifyCode::generateVerifyCode();
        $verify->expiration_time = $expirationTime;
        $verify->save();
        return $verify;
    }

    public function getVerifyCodeInfo($orderId, $scenicId,  $columns = ['*'])
    {
        return ScenicOrderVerifyCode::query()->where('order_id', $orderId)->where('scenic_id', $scenicId)->first($columns);
    }

    public function getByCode($code, $columns = ['*'])
    {
        return ScenicOrderVerifyCode::query()->where('code', $code)->where('status', 0)->first($columns);
    }

    public function getById($id, $columns = ['*'])
    {
        return ScenicOrderVerifyCode::query()->find($id, $columns);
    }

    public function getPendingListByOrderId($orderId, $columns = ['*'])
    {
        return ScenicOrderVerifyCode::query()->where('order_id', $orderId)->where('status', 0)->get($columns);
    }

    public function hasUnverifiedCodes($orderId)
    {
        return ScenicOrderVerifyCode::query()
            ->where('order_id', $orderId)
            ->where('status', 0)
            ->exists();
    }

    public function verify(ScenicOrderVerifyCode $verifyCodeInfo, $userId)
    {
        $verifyCodeInfo->status = 1;
        $verifyCodeInfo->save();

        $log = ScenicOrderVerifyLog::new();
        $log->order_id = $verifyCodeInfo->order_id;
        $log->scenic_id = $verifyCodeInfo->scenic_id;
        $log->verifier_id = $userId;
        $log->verify_time = now()->format('Y-m-d\TH:i:s');
        $log->save();
    }
}
