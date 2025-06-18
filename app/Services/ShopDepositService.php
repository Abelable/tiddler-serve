<?php

namespace App\Services;

use App\Models\ShopDeposit;

class ShopDepositService extends BaseService
{
    public function getShopDeposit($shopId)
    {
        $deposit = ShopDeposit::query()->where('shop_id', $shopId)->first();
        if (is_null($deposit)) {
            $deposit = $this->createShopDeposit($shopId);
        }
        return $deposit;
    }

    public function createShopDeposit($userId)
    {
        $deposit = ShopDeposit::new();
        $deposit->shop_id = $userId;
        $deposit->save();
        return $deposit;
    }

    public function updateDeposit($shopId, $type, $amount, $referenceId = '')
    {
        $deposit = $this->getShopDeposit($shopId);
        $oldBalance = $deposit->balance;
        $newBalance = bcadd($oldBalance, $amount, 2);
        $deposit->balance = $newBalance;
        $deposit->save();

        ShopDepositChangeLogService::getInstance()
            ->createLog($deposit->id, $oldBalance, $newBalance, $type, $amount, $referenceId);
    }
}
