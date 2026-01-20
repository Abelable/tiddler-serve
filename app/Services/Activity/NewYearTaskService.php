<?php

namespace App\Services\Activity;

use App\Models\Activity\NewYearTask;
use App\Models\Activity\NewYearUserTask;
use App\Services\BaseService;
use App\Services\RelationService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Activity\NewYearTaskInput;
use App\Utils\Inputs\TypePageInput;

class NewYearTaskService extends BaseService
{
    public function getPage(TypePageInput $input, $columns = ['*'])
    {
        $query = NewYearTask::query();
        if (!empty($input->type)) {
            $query = $query->where('type', $input->type);
        }
        return $query
            ->orderByRaw('status = 1 DESC')
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
        $task->type = $input->type;
        $task->time_limit = $input->timeLimit ?? 0;
        $task->icon = $input->icon;
        $task->name = $input->name;
        $task->desc = $input->desc;
        $task->btn_content = $input->btnContent;
        $task->luck_score = $input->luckScore;
        $task->scene = $input->scene;
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

    public function finishInviteTask($userId)
    {
        $todayNewComerCount = RelationService::getInstance()->getTodayCountBySuperiorId($userId);
        $todayLuck = NewYearLuckService::getInstance()->getTodayLuckByUserId($userId, 100);

        if ($todayNewComerCount >= 4 && is_null($todayLuck)) {
            NewYearLuckService::getInstance()
                ->createLuck($userId, '成功邀请新人助力', 1, 80, 100, 2);
        }
    }

    public function finishMerchantInviteTask($taskId, $merchantId)
    {
        $userTask = $this->getUserTaskByTaskId($taskId, $merchantId);
        if (!is_null($userTask) && $userTask->status == 0) {
            $userTask->status = 1;
            $userTask->save();

            $this->finishTask($userTask->user_id, $taskId, $merchantId);
        }
    }

    public function finishTask(int $userId, int $taskId, string $referenceId = ''): void
    {
        $task = $this->getTaskById($taskId);
        if (!$task || $task->status != 1) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '任务不存在');
        }

        $luckCount = NewYearLuckService::getInstance()->getLuckCount($userId, $taskId);
        if ($task->time_limit != 0 && $luckCount >= $task->time_limit) {
            $this->throwBusinessException(CodeResponse::INVALID_OPERATION, '超任务次数限制了');
        }

        $luck = NewYearLuckService::getInstance()->getLuckByUserId($userId, $taskId);
        $todayLuck = NewYearLuckService::getInstance()->getTodayLuckByUserId($userId, $taskId);

        $shouldGrant = false;
        if ($task->type == 1) {
            $shouldGrant = is_null($luck);
        } elseif ($task->type == 2) {
            $shouldGrant = is_null($todayLuck);
        } elseif ($task->type == 3) {
            $shouldGrant = true;
        }

        if (!$shouldGrant) {
            return; // 幂等，不抛异常
        }

        // 创建福气值记录
        NewYearLuckService::getInstance()->createLuck(
            $userId,
            $task->name,
            1,
            $task->luck_score,
            $task->id,
            $task->type,
            $referenceId
        );
    }

    public function createUserTask($userId, $taskId, $referenceId = null)
    {
        $userTask = NewYearUserTask::new();
        $userTask->user_id = $userId;
        $userTask->task_id = $taskId;
        $userTask->reference_id = $referenceId;
        $userTask->save();
        return $userTask;
    }

    public function getUserTaskByTaskId($taskId, $referenceId = null)
    {
        $query = NewYearUserTask::query()->where('task_id', $taskId);
        if (!is_null($referenceId)) {
            $query->where('reference_id', $referenceId);
        }
        return $query->first();
    }
}
