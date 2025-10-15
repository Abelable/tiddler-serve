<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\TaskOfInviteMerchant;
use App\Models\UserTask;
use App\Services\TaskService;
use App\Services\UserTaskService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;
use App\Utils\Inputs\TaskPageInput;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    protected $except = ['list'];

    public function list()
    {
        /** @var TaskPageInput $input */
        $input = TaskPageInput::new();
        $list = TaskService::getInstance()->getTaskPage($input);
        return $this->successPaginate($list);
    }

    public function receiveTask()
    {
        $id = $this->verifyRequiredId('id');
        $task = TaskService::getInstance()->getTaskByStatus($id, 1);
        if (is_null($task)) {
            return $this->fail(CodeResponse::NOT_FOUND, '任务不存在，或已被领取');
        }

        DB::transaction(function () use ($task) {
            UserTaskService::getInstance()->createUserTask($this->userId(), $task);

            $task->status = 2;
            $task->save();
        });

        return $this->success();
    }

    public function userTaskData()
    {
        $finishedTaskCount = UserTaskService::getInstance()->getUserTaskCount($this->userId());
        $taskRewardTotal = UserTaskService::getInstance()->getUserRewardTotal($this->userId());

        return $this->success([
            'finishedTaskCount' => $finishedTaskCount,
            'taskRewardTotal' => $taskRewardTotal
        ]);
    }

    public function userTaskList()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();
        $statusList = $input->status == 0 ? [1, 2, 3, 4, 5, 6] : ($input->status == 2 ? [2, 3, 4, 5] : [$input->status]);

        $page = UserTaskService::getInstance()
            ->getUserTaskPage($this->userId(), $statusList, $input);
        $userTaskList = collect($page->items());

        $taskIds = $userTaskList->pluck('task_id')->toArray();
        $taskList = TaskService::getInstance()->getTaskListByIds($taskIds)->keyBy('id');

        $list = $userTaskList->map(function (UserTask $userTask) use ($taskList) {
            /** @var TaskOfInviteMerchant $task */
            $task = $taskList->get($userTask->task_id);

            $userTask['productName'] = $task->product_name;
            $userTask['tel'] = $task->tel;
            $userTask['address'] = $task->address;
            $userTask['longitude'] = $task->longitude;
            $userTask['latitude'] = $task->latitude;

            unset($userTask->user_id);

            return $userTask;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function taskDetail()
    {
        $taskId = $this->verifyRequiredId('taskId');

        $userTask = UserTaskService::getInstance()->getUserTask($this->userId(), $taskId);
        if (is_null($userTask)) {
            return $this->fail(CodeResponse::NOT_FOUND, '用户未领取当前任务');
        }

        $task = TaskService::getInstance()->getTaskById($taskId);
        if (is_null($task)) {
            return $this->fail(CodeResponse::NOT_FOUND, '任务不存在');
        }

        $userTask['productName'] = $task->product_name;
        $userTask['tel'] = $task->tel;
        $userTask['address'] = $task->address;
        $userTask['longitude'] = $task->longitude;
        $userTask['latitude'] = $task->latitude;
        $userTask['rewardList'] = json_decode($task->reward_list);

        unset($userTask->user_id);

        return $this->success($userTask);
    }
}
