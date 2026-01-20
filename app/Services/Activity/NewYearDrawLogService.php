<?php

namespace App\Services\Activity;

use App\Models\Activity\NewYearDrawLog;
use App\Models\Activity\NewYearPrize;
use App\Services\BaseService;

class NewYearDrawLogService extends BaseService
{
    public function createLog($userId, NewYearPrize $prize = null)
    {
        $log = NewYearDrawLog::new();
        $log->user_id = $userId;
        if (!is_null($prize)) {
            $log->prize_id = $prize->id;
            $log->prize_type = $prize->type;
            $log->prize_cover = $prize->cover;
            $log->prize_name = $prize->name;
            $log->prize_cost = $prize->cost;
        }
        $log->save();

        return $log;
    }
}
