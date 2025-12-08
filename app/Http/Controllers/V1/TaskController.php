<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Task\TaskOfInviteMerchant;
use App\Models\Task\UserTask;
use App\Services\Task\TaskService;
use App\Services\Task\UserTaskService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;
use App\Utils\Inputs\TaskPageInput;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    protected $except = ['list', 'status'];

    public function list()
    {
        /** @var TaskPageInput $input */
        $input = TaskPageInput::new();
        $list = TaskService::getInstance()->getTaskPage($input);
        return $this->successPaginate($list);
    }

    public function pickTask()
    {
        $id = $this->verifyRequiredId('id');
        $task = TaskService::getInstance()->getTaskByStatus($id, [1]);
        if (is_null($task)) {
            return $this->fail(CodeResponse::NOT_FOUND, '任务不存在，或已被领取');
        }

        $userTask = UserTaskService::getInstance()->getUserTaskByStatus($this->userId(), $id, [6]);

        DB::transaction(function () use ($userTask, $task) {
            if (!is_null($userTask)) {
                $userTask->status = 1;
                $userTask->pick_time = now();
                $userTask->save();
            } else {
                UserTaskService::getInstance()->createUserTask($this->userId(), $task);
            }

            $task->status = 2;
            $task->save();
        });

        return $this->success();
    }

    public function cancelTask()
    {
        $id = $this->verifyRequiredId('id');
        $userTask = UserTaskService::getInstance()->getUserTaskByStatus($this->userId(), $id, [1]);
        if (is_null($userTask)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '当前任务不可取消');
        }

        $task = TaskService::getInstance()->getTaskById($id);
        if (is_null($task)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前任务不存在');
        }

        DB::transaction(function () use ($task, $userTask) {
            $userTask->status = 6;
            $userTask->save();

            $task->status = 1;
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

            $userTask['merchantName'] = $task->merchant_name;
            $userTask['tel'] = $task->tel;
            $userTask['address'] = $task->address;
            $userTask['longitude'] = $task->longitude;
            $userTask['latitude'] = $task->latitude;

            unset($userTask->user_id);

            return $userTask;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $userTask = UserTaskService::getInstance()->getUserTask($this->userId(), $id);
        if (is_null($userTask)) {
            return $this->fail(CodeResponse::NOT_FOUND, '用户未领取当前任务');
        }

        $task = TaskService::getInstance()->getTaskById($id);
        if (is_null($task)) {
            return $this->fail(CodeResponse::NOT_FOUND, '任务不存在');
        }

        $userTask['taskStatus'] = $task->status;
        $userTask['merchantName'] = $task->merchant_name;
        $userTask['tel'] = $task->tel;
        $userTask['address'] = $task->address;
        $userTask['longitude'] = $task->longitude;
        $userTask['latitude'] = $task->latitude;
        $userTask['rewardList'] = json_decode($task->reward_list);

        unset($userTask->user_id);

        return $this->success($userTask);
    }

    public function status()
    {
        $userId = $this->verifyRequiredId('userId');
        $taskId = $this->verifyRequiredId('taskId');

        $userTask = UserTaskService::getInstance()->getUserTask($userId, $taskId);
        if (is_null($userTask)) {
            return $this->fail(CodeResponse::NOT_FOUND, '请确认是否领取任务');
        }

        return $this->success($userTask->status);
    }
}
