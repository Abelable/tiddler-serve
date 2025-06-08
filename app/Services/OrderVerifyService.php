<?php

namespace App\Services;

use App\Models\OrderVerifyCode;
use App\Models\OrderVerifyLog;

class OrderVerifyService extends BaseService
{
    public function createVerifyCode($orderId, $expirationTime = '')
    {
        $verify = OrderVerifyCode::new();
        $verify->order_id = $orderId;
        $verify->code = OrderVerifyCode::generateVerifyCode();
        $verify->expiration_time = $expirationTime;
        $verify->save();
        return $verify;
    }

    public function getByCode($code, $columns = ['*'])
    {
        return OrderVerifyCode::query()->where('code', $code)->where('status', 0)->first($columns);
    }

    public function getByOrderId($orderId, $columns = ['*'])
    {
        return OrderVerifyCode::query()->where('order_id', $orderId)->first($columns);
    }

    public function getById($id, $columns = ['*'])
    {
        return OrderVerifyCode::query()->find($id, $columns);
    }

    public function verify(OrderVerifyCode $verifyCodeInfo, $userId, $shopId)
    {
        $verifyCodeInfo->status = 1;
        $verifyCodeInfo->save();

        $log = OrderVerifyLog::new();
        $log->verify_code_id = $verifyCodeInfo->id;
        $log->shop_id = $shopId;
        $log->verifier_id = $userId;
        $log->verify_time = now()->format('Y-m-d\TH:i:s');
        $log->save();
    }
}
