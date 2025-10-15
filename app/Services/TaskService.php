<?php

namespace App\Services;

use App\Models\TaskOfInviteMerchant;
use App\Utils\Inputs\Admin\TaskInput;
use App\Utils\Inputs\TaskPageInput;

class TaskService extends BaseService
{
    public function updateTask(TaskOfInviteMerchant $task, TaskInput $input)
    {
        $task->product_type = $input->productType;
        $task->product_id = $input->productId;
        $task->product_name = $input->productName;
        $task->tel = $input->tel;
        $task->address = $input->address;
        $task->longitude = $input->longitude;
        $task->latitude = $input->latitude;
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
        if (!is_null($input->productType)) {
            $query->where('product_type', $input->productType);
        }
        if (!is_null($input->productName)) {
            $query->where('product_name', 'like', "%$input->productName%");
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getTaskById($id, $columns = ['*'])
    {
        return TaskOfInviteMerchant::query()->find($id, $columns);
    }
}
