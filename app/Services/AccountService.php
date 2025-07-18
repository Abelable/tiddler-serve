<?php

namespace App\Services;

use App\Models\Account;

class AccountService extends BaseService
{
    public function getUserAccount($userId)
    {
        $account = Account::query()->where('user_id', $userId)->first();
        if (is_null($account)) {
            $account = $this->createUserAccount($userId);
        }
        return $account;
    }

    public function createUserAccount($userId)
    {
        $account = Account::new();
        $account->user_id = $userId;
        $account->save();
        return $account;
    }

    public function updateBalance($userId, $type, $amount, $referenceId = '', $productType = 0)
    {
        $account = $this->getUserAccount($userId);
        $oldBalance = $account->balance ?? 0;
        $newBalance = bcadd($oldBalance, $amount, 2);
        $account->balance = $newBalance;
        $account->save();

        AccountChangeLogService::getInstance()
            ->createLog($account->id, $oldBalance, $newBalance, $type, $amount, $referenceId, $productType);
    }
}
