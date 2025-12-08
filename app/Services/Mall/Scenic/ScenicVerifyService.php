<?php

namespace App\Services\Mall\Scenic;

use App\Models\Mall\Scenic\ScenicVerifyCode;
use App\Services\BaseService;

class ScenicVerifyService extends BaseService
{
    public function createVerifyCode($orderId, $scenicId, $expirationTime = null)
    {
        $verify = ScenicVerifyCode::new();
        $verify->order_id = $orderId;
        $verify->scenic_id = $scenicId;
        $verify->code = ScenicVerifyCode::generateVerifyCode();
        if ($expirationTime) {
            $verify->expiration_time = $expirationTime;
        }
        $verify->save();
        return $verify;
    }

    public function getVerifyCodeInfo($orderId, $scenicId,  $columns = ['*'])
    {
        return ScenicVerifyCode::query()->where('order_id', $orderId)->where('scenic_id', $scenicId)->first($columns);
    }

    public function getByCode($code, $columns = ['*'])
    {
        return ScenicVerifyCode::query()
            ->where('code', $code)
            ->where('status', 0)
            ->first($columns);
    }

    public function getById($id, $columns = ['*'])
    {
        return ScenicVerifyCode::query()->find($id, $columns);
    }

    public function getPendingListByOrderId($orderId, $columns = ['*'])
    {
        return ScenicVerifyCode::query()->where('order_id', $orderId)->where('status', 0)->get($columns);
    }

    public function hasUnverifiedCodes($orderId)
    {
        return ScenicVerifyCode::query()
            ->where('order_id', $orderId)
            ->where('status', 0)
            ->exists();
    }

    public function verify(ScenicVerifyCode $verifyCodeInfo, $userId)
    {
        $verifyCodeInfo->status = 1;
        $verifyCodeInfo->verifier_id = $userId;
        $verifyCodeInfo->verify_time = now();
        $verifyCodeInfo->save();
        return $verifyCodeInfo;
    }
}
