<?php

namespace App\Services\Mall\Catering;

use App\Models\Catering\CateringShopDeposit;
use App\Services\BaseService;

class CateringShopDepositService extends BaseService
{
    public function getShopDeposit($shopId)
    {
        $deposit = CateringShopDeposit::query()->where('shop_id', $shopId)->first();
        if (is_null($deposit)) {
            $deposit = $this->createShopDeposit($shopId);
        }
        return $deposit;
    }

    public function createShopDeposit($shopId)
    {
        $deposit = CateringShopDeposit::new();
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

        CateringShopDepositChangeLogService::getInstance()
            ->createLog($shopId, $oldBalance, $newBalance, $type, $amount, $referenceId);
    }
}
