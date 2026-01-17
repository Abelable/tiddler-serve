<?php

namespace App\Services\Activity;

use App\Models\Activity\NewYearLuck;
use App\Models\Activity\NewYearTask;
use App\Models\Activity\NewYearUserLuck;
use App\Services\BaseService;
use App\Services\RelationService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Activity\NewYearTaskInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class NewYearTaskService extends BaseService
{
    public function getPage(PageInput $input, $columns = ['*'])
    {
        return NewYearTask::query()
            ->orderBy('sort', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getList($columns = ['*'])
    {
        return NewYearTask::query()
            ->where('status', 1)
            ->orderBy('sort', 'desc')
            ->orderBy('id', 'asc')
            ->get($columns);
    }

    public function updateTask(NewYearTask $task, NewYearTaskInput $input)
    {
        $task->icon = $input->icon;
        $task->name = $input->name;
        $task->desc = $input->desc;
        $task->btn_content = $input->btnContent;
        $task->luck_score = $input->luckScore;
        $task->type = $input->type;
        $task->param = $input->param ?? '';
        $task->save();
        return $task;
    }

    public function getTaskById($id, $columns = ['*'])
    {
        return NewYearTask::query()->find($id, $columns);
    }

    public function updateSort($id, $sort)
    {
        NewYearTask::query()->where('id', $id)->update(['sort' => $sort]);
    }

    public function finishInviteTask($superiorId)
    {
        $todayNewComerCount = RelationService::getInstance()->getTodayCountBySuperiorId($superiorId);
        $todayLuck = $this->getTodayLuckByUserId($superiorId, 0);

        if ($todayNewComerCount >= 4 && is_null($todayLuck)) {
            try {
                $this->createLuck($superiorId, '成功邀请新人助力', 1, 80);
                $this->updateUserLuck($superiorId, 80);
            } catch (\Exception $e) {
                // 23000 = MySQL 唯一约束冲突
                if ($e->getCode() === '23000') {
                    Log::warning('重复添加福气值（已被唯一索引拦截）', [
                        'user_id' => $superiorId,
                    ]);
                    return;
                }
                throw $e;
            }
        }
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
