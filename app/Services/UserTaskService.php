<?php

namespace App\Services;

use App\Models\UserTask;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

class UserTaskService extends BaseService
{
    public function getUserTaskPage($userId, $statusList, PageInput $input, $columns = ['*'])
    {
        return UserTask::query()
            ->where('user_id', $userId)
            ->whereIn('status', $statusList)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserTaskList($userId, $columns = ['*'])
    {
        return UserTask::query()
            ->where('status', 1)
            ->where('user_id', $userId)
            ->get($columns);
    }

    public function getUsedCount($userId)
    {
        return UserTask::query()
            ->where('user_id', $userId)
            ->where('status', 2)
            ->where('created_at', '>=', now()->subDays(7))
            ->select('task_id', DB::raw('count(*) as receive_count'))
            ->groupBy('task_id')
            ->get();
    }

    public function getListByTaskIds($userId, array $taskIds, $columns = ['*'])
    {
        return UserTask::query()
            ->where('status', 1)
            ->where('user_id', $userId)
            ->whereIn('task_id', $taskIds)
            ->get($columns);
    }

    public function getUserTask($userId, $taskId, $columns = ['*'])
    {
        return UserTask::query()
            ->where('status', 1)
            ->where('user_id', $userId)
            ->where('task_id', $taskId)
            ->first($columns);
    }

    public function getUserUsedTaskByTaskId($userId, $taskId, $columns = ['*'])
    {
        return UserTask::query()
            ->where('status', 2)
            ->where('user_id', $userId)
            ->where('task_id', $taskId)
            ->first($columns);
    }

    public function useTask($userId, $taskId)
    {
        $userTask = $this->getUserTask($userId, $taskId);
        $userTask->status = 2;
        $userTask->save();
        return $userTask;
    }

    public function deleteByTaskId($taskId)
    {
        return UserTask::query()->where('task_id', $taskId)->delete();
    }

    public function expireTask($taskId)
    {
        $taskList = $this->getListByTaskId($taskId);
        foreach ($taskList as $task) {
            if (is_null($task)) {
                $this->throwBusinessException(CodeResponse::NOT_FOUND, '用户领取的优惠券不存在');
            }
            $task->status = 3;
            $task->save();
        }
    }

    public function getListByTaskId($taskId, $columns = ['*'])
    {
        return UserTask::query()->where('task_id', $taskId)->get($columns);
    }

    public function getReceivedCount($userId, $taskId)
    {
        return UserTask::query()->where('user_id', $userId)->where('task_id', $taskId)->count();
    }
}
