<?php

namespace App\Services\Mall\Hotel;

use App\Models\Mall\Hotel\HotelShopDeposit;
use App\Services\BaseService;

class HotelShopDepositService extends BaseService
{
    public function createShopDeposit($shopId)
    {
        $deposit = HotelShopDeposit::new();
        $deposit->shop_id = $shopId;
        $deposit->save();
        return $deposit;
    }

    public function updateDeposit($shopId, $type, $amount, $referenceId = '')
    {
        $deposit = $this->getShopDeposit($shopId);
        $oldBalance = $deposit->balance ?? 0;
        $newBalance = bcadd($oldBalance, $amount, 2);
        $deposit->balance = $newBalance;
        $deposit->save();

        HotelShopDepositChangeLogService::getInstance()
            ->createLog($shopId, $oldBalance, $newBalance, $type, $amount, $referenceId);
    }

    public function getShopDeposit($shopId)
    {
        return HotelShopDeposit::query()->where('shop_id', $shopId)->first();
    }
}
