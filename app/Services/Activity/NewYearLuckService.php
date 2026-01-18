<?php

namespace App\Services\Activity;

use App\Models\Activity\NewYearLuck;
use App\Models\Activity\NewYearUserLuck;
use App\Services\BaseService;
use Illuminate\Support\Carbon;

class NewYearLuckService extends BaseService
{
    public function getLuckByUserId($userId, $taskId, $columns = ['*'])
    {
        return NewYearLuck::query()
            ->where('user_id', $userId)
            ->where('task_id', $taskId)
            ->first($columns);
    }

    public function getTodayLuckByUserId($userId, $taskId, $columns = ['*'])
    {
        $start = Carbon::today()->startOfDay();
        $end   = Carbon::today()->endOfDay();
        return NewYearLuck::query()
            ->whereBetween('created_at', [$start, $end])
            ->where('user_id', $userId)
            ->where('task_id', $taskId)
            ->first($columns);
    }

    public function createLuck($userId, $desc, $type, $score, $taskId = 0)
    {
        $luck = NewYearLuck::new();
        $luck->user_id = $userId;
        $luck->task_id = $taskId;
        $luck->desc = $desc;
        $luck->type = $type;
        $luck->score = $score;

        // 用于唯一索引生成（防并发、防重复）
        $luck->task_date = Carbon::today()->toDateString();

        $luck->save();
    }

    public function updateUserLuck($userId, $score)
    {
        $userLuck = $this->getUserLuck($userId);
        $userLuck->score = bcadd($userLuck->score, $score);
        $userLuck->save();
    }

    public function getUserLuck($userId, $columns = ['*'])
    {
        $userLuck = NewYearUserLuck::query()->where('user_id', $userId)->first($columns);
        if (is_null($userLuck)) {
            $userLuck = NewYearUserLuck::new();
            $userLuck->user_id = $userId;
            $userLuck->save();
        }
        return $userLuck;
    }
}
