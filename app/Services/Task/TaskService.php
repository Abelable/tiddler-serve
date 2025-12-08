<?php

namespace App\Services\Task;

use App\Models\Task\TaskOfInviteMerchant;
use App\Services\BaseService;
use App\Utils\Inputs\Admin\TaskInput;
use App\Utils\Inputs\TaskPageInput;

class TaskService extends BaseService
{
    public function updateTask(TaskOfInviteMerchant $task, TaskInput $input)
    {
        $task->merchant_type = $input->merchantType;
        $task->product_id = $input->productId ?? 0;
        $task->merchant_name = $input->merchantName;
        $task->tel = $input->tel;
        $task->address = $input->address;
        $task->longitude = $input->longitude ?? 0;
        $task->latitude = $input->latitude ?? 0;
        $task->reward_total = $input->rewardTotal;
        $task->reward_list = json_encode($input->rewardList);
        $task->save();
        return $task;
    }

    public function getTaskPage(TaskPageInput $input, $columns = ['*'])
    {
        $query = TaskOfInviteMerchant::query();
        if (!is_null($input->status)) {
            $query->where('status', $input->status);
        }
        if (!is_null($input->merchantType)) {
            $query->where('merchant_type', $input->merchantType);
        }
        if (!is_null($input->merchantName)) {
            $query->where('merchant_name', 'like', "%$input->merchantName%");
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getTaskById($id, $columns = ['*'])
    {
        return TaskOfInviteMerchant::query()->find($id, $columns);
    }

    public function getTaskByStatus($id, array $statusList, $columns = ['*'])
    {
        return TaskOfInviteMerchant::query()->whereIn('status', $statusList)->where('id', $id)->first($columns);
    }

    public function getTaskListByIds(array $ids, $columns = ['*'])
    {
        return TaskOfInviteMerchant::query()->whereIn('id', $ids)->get($columns);
    }
}
