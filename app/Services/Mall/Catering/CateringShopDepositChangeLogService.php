<?php

namespace App\Services\Mall\Catering;

use App\Models\Mall\Catering\CateringShopDepositChangeLog;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;

class CateringShopDepositChangeLogService extends BaseService
{
    public function getLogPage($shopId, PageInput $input, $columns = ['*'])
    {
        return CateringShopDepositChangeLog::query()
            ->where('shop_id', $shopId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function createLog($shopId, $oldBalance, $newBalance, $changeType, $changeAmount, $referenceId = '')
    {
        $log = CateringShopDepositChangeLog::new();
        $log->shop_id = $shopId;
        $log->old_balance = $oldBalance;
        $log->new_balance = $newBalance;
        $log->change_type = $changeType;
        $log->change_amount = $changeAmount;
        $log->reference_id = $referenceId;
        $log->save();
        return $log;
    }
}
