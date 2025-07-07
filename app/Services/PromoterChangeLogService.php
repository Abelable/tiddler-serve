<?php

namespace App\Services;

use App\Models\PromoterChangeLog;
use App\Utils\Inputs\PageInput;

class PromoterChangeLogService extends BaseService
{
    public function getLogPage(PageInput $input, $columns = ['*'])
    {
        return PromoterChangeLog::query()
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function createLog(
        $promoterId,
        $changeType,
        $oldLevel = 0,
        $newLevel = 1,
        $oldExpirationTime = '',
        $newExpirationTime = '',
        $oldGiftGoodsId = 0,
        $newGiftGoodsId = 0
    )
    {
        $log = PromoterChangeLog::new();
        $log->promoter_id = $promoterId;
        $log->change_type = $changeType;

        if ($changeType == 1) {
            $log->old_level = $oldLevel;
            $log->new_level = $newLevel;
        }
        if ($changeType == 2) {
            $log->old_expiration_time = $oldExpirationTime;
            $log->new_expiration_time = $newExpirationTime;
            $log->old_gift_goods_id = $oldGiftGoodsId;
            $log->new_gift_goods_id = $newGiftGoodsId;
        }

        $log->save();

        return $log;
    }

    public function getLevelChangeLog($promoterId, $columns = ['*'])
    {
        return PromoterChangeLog::query()
            ->where('promoter_id', $promoterId)
            ->where('change_type', 1)
            ->orderBy('id', 'desc')
            ->first($columns);
    }
}
