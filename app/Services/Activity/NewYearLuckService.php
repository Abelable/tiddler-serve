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

    public function getUserLuckScore($userId)
    {
        return NewYearLuck::query()->where('user_id', $userId)->sum('score');
    }

    public function getUserLuckList($userId, PageInput $input, $columns = ['*'])
    {
        return NewYearLuck::query()
            ->where('user_id', $userId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }
}
