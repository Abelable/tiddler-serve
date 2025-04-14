<?php

namespace App\Services;

use App\Models\OrderVerifyCode;

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
}
