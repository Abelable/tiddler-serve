<?php

namespace App\Services;

use App\Models\OrderVerifyCode;
use App\Models\OrderVerifyLog;

class OrderVerifyService extends BaseService
{
    public function createVerifyCode($orderId)
    {
        $verify = OrderVerifyCode::new();
        $verify->order_id = $orderId;
        $verify->code = OrderVerifyCode::generateVerifyCode();
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

    public function verify($id, $userId, $shopId)
    {
        $verifyInfo = $this->getById($id);
        $verifyInfo->status = 1;
        $verifyInfo->save();

        $log = OrderVerifyLog::new();
        $log->verify_code_id = $verifyInfo->id;
        $log->shop_id = $shopId;
        $log->verifier_id = $userId;
        $log->verify_time = now()->toDateTimeString();
        $log->save();
    }
}
