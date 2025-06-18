<?php

namespace App\Services;

use App\Models\AccountChangeLog;
use App\Utils\Inputs\PageInput;

class AccountChangeLogService extends BaseService
{
    public function getLogPage($accountId, PageInput $input, $columns = ['*'])
    {
        return AccountChangeLog::query()
            ->where('account_id', $accountId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function createLog($accountId, $oldBalance, $newBalance, $changeType, $changeAmount, $referenceId = '', $productType = 0)
    {
        $log = new AccountChangeLog();
        $log->account_id = $accountId;
        $log->old_balance = $oldBalance;
        $log->new_balance = $newBalance;
        $log->change_type = $changeType;
        $log->change_amount = $changeAmount;
        $log->reference_id = $referenceId;
        $log->product_type = $productType;
        $log->save();
        return $log;
    }
}
