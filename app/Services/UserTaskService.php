<?php

namespace App\Services;

use App\Models\TaskOfInviteMerchant;
use App\Models\UserTask;
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

    public function createUserTask($userId, TaskOfInviteMerchant $task)
    {
        $userTask = UserTask::new();
        $userTask->user_id = $userId;
        $userTask->task_id = $task->id;
        $userTask->task_reward = $task->reward_total;
        $userTask->merchant_type = $task->merchant_type;
        $userTask->product_id = $task->product_id;
        $userTask->pick_time = now()->toDateTimeString();
        $userTask->save();
        return $userTask;
    }

    public function getUserTask($userId, $taskId, $columns = ['*'])
    {
        return UserTask::query()
            ->where('user_id', $userId)
            ->where('task_id', $taskId)
            ->first($columns);
    }

    public function getUserTaskByStatus($userId, $taskId, array $statusList, $columns = ['*'])
    {
        return UserTask::query()
            ->whereIn('status', [$statusList])
            ->where('user_id', $userId)
            ->where('task_id', $taskId)
            ->first($columns);
    }

    public function getByMerchantId($merchantType, $merchantId, $step, $columns = ['*'])
    {
        return UserTask::query()
            ->where('status', 1)
            ->where('merchant_type', $merchantType)
            ->where('merchant_id', $merchantId)
            ->where('step', $step)
            ->first($columns);
    }

    public function getByOrderId($merchantType, $orderId, $productType = null, $columns = ['*'])
    {
        $query = UserTask::query()
            ->where('status', 1)
            ->where('step', 4)
            ->where('merchant_type', $merchantType)
            ->where('order_id', $orderId);
        if (!is_null($productType)) {
            $query = $query->where('product_type', $productType);
        }
        return $query->first($columns);
    }

    public function deleteByTaskId($taskId)
    {
        return UserTask::query()->where('task_id', $taskId)->delete();
    }

    public function getListByTaskId($taskId, $columns = ['*'])
    {
        return UserTask::query()->where('task_id', $taskId)->get($columns);
    }

    public function getUserTaskCount($userId)
    {
        return UserTask::query()
            ->where('user_id', $userId)
            ->where('status', [2, 3, 4, 5])
            ->count();
    }

    public function getUserRewardTotal($userId)
    {
        return UserTask::query()
            ->where('user_id', $userId)
            ->where('status', [2, 3, 4, 5])
            ->sum('task_reward');
    }
}
