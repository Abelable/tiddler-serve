<?php

namespace App\Services\Mall\Goods;

use App\Models\Mall\Goods\ShopDeposit;
use App\Services\BaseService;

class ShopDepositService extends BaseService
{
    public function createShopDeposit($shopId)
    {
        $deposit = ShopDeposit::new();
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

        ShopDepositChangeLogService::getInstance()
            ->createLog($shopId, $oldBalance, $newBalance, $type, $amount, $referenceId);
    }

    public function getShopDeposit($shopId, $columns = ['*'])
    {
        return ShopDeposit::query()->where('shop_id', $shopId)->first($columns);
    }
}
