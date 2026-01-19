<?php

namespace App\Services\Activity;

use App\Models\Activity\NewYearLuck;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;
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

    public function getLuckCount($userId, $taskId)
    {
        return NewYearLuck::query()
            ->where('user_id', $userId)
            ->where('task_id', $taskId)
            ->count();
    }

    public function createLuck($userId, $desc, $type, $score, $taskId = 0, $taskType = 2, $referenceId = '')
    {
        // 活动截止时间2026.02.24
        if (Carbon::now()->greaterThan(Carbon::create(2026, 2, 24, 23, 59, 59))) {
            return false;
        }

        $luck = NewYearLuck::new();
        $luck->user_id = $userId;
        $luck->task_id = $taskId;
        $luck->desc = $desc;
        $luck->type = $type;
        $luck->score = $score;
        $luck->reference_id = $referenceId;

        // 用于唯一索引生成（防并发、防重复）
        if ($taskType == 3) {
            $luck->task_date = Carbon::now()->toDateTimeString();
        } else {
            $luck->task_date = Carbon::today()->toDateTimeString();
        }

        $luck->save();
        return true;
    }

    public function getUserLuckScore($userId)
    {
        return NewYearLuck::query()->where('user_id', $userId)->sum('score');
    }

    public function getUserLuckPage($userId, PageInput $input, $columns = ['*'])
    {
        return NewYearLuck::query()
            ->where('user_id', $userId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserLuckList($userId, $columns = ['*'])
    {
        return NewYearLuck::query()->where('user_id', $userId)->get($columns);
    }

    public function getLuckCountMap(int $userId)
    {
        return NewYearLuck::query()
            ->selectRaw('task_id, COUNT(*) as count')
            ->where('user_id', $userId)
            ->groupBy('task_id')
            ->pluck('count', 'task_id');
    }

    public function getUserLatestLuckList($userId, $columns = ['*'])
    {
        return NewYearLuck::query()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get($columns)
            ->keyBy('task_id');
    }

    public function deleteLuck($userId, $taskId)
    {
        NewYearLuck::query()->where('user_id', $userId)->where('task_id', $taskId)->delete();
    }
}
