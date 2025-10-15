<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskOfInviteMerchant;
use App\Services\TaskService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\TaskInput;
use App\Utils\Inputs\TaskPageInput;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var TaskPageInput $input */
        $input = TaskPageInput::new();
        $list = TaskService::getInstance()->getTaskPage($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $task = TaskService::getInstance()->getTaskById($id);
        if (is_null($task)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前任务不存在');
        }
        return $this->success($task);
    }

    public function add()
    {
        /** @var TaskInput $input */
        $input = TaskInput::new();

        $task = TaskOfInviteMerchant::new();
        TaskService::getInstance()->updateTask($task, $input);

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var TaskInput $input */
        $input = TaskInput::new();

        $task = TaskService::getInstance()->getTaskById($id);
        if (is_null($task)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前任务不存在');
        }

        TaskService::getInstance()->updateTask($task, $input);

        return $this->success();
    }

    public function up()
    {
        $id = $this->verifyRequiredId('id');
        $task = TaskService::getInstance()->getTaskById($id);
        if (is_null($task)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前任务不存在');
        }

        $task->status = 1;
        $task->save();

        return $this->success();
    }

    public function down()
    {
        $id = $this->verifyRequiredId('id');
        $task = TaskService::getInstance()->getTaskById($id);
        if (is_null($task)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前任务不存在');
        }

        $task->status = 4;
        $task->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $task = TaskService::getInstance()->getTaskById($id);
        if (is_null($task)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前任务不存在');
        }

        $task->delete();

        return $this->success();
    }
}
