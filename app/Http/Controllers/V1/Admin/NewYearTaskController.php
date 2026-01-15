<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity\NewYearTask;
use App\Services\Activity\NewYearTaskService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Activity\NewYearTaskInput;
use App\Utils\Inputs\PageInput;

class NewYearTaskController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $list = NewYearTaskService::getInstance()->getPage($input);
        return $this->successPaginate($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $task = NewYearTaskService::getInstance()->getTaskById($id);
        if (is_null($task)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前任务不存在');
        }
        return $this->success($task);
    }

    public function add()
    {
        /** @var NewYearTaskInput $input */
        $input = NewYearTaskInput::new();

        $task = NewYearTask::new();
        NewYearTaskService::getInstance()->updateTask($task, $input);

        return $this->success();
    }

    public function edit()
    {
        /** @var NewYearTaskInput $input */
        $input = NewYearTaskInput::new();
        $id = $this->verifyRequiredId('id');

        $task = NewYearTaskService::getInstance()->getTaskById($id);
        if (is_null($task)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前任务不存在');
        }

        NewYearTaskService::getInstance()->updateTask($task, $input);

        return $this->success();
    }

    public function editSort()
    {
        $id = $this->verifyRequiredId('id');
        $sort = $this->verifyRequiredInteger('sort');

        $task = NewYearTaskService::getInstance()->getTaskById($id);
        if (is_null($task)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前任务不存在');
        }

        NewYearTaskService::getInstance()->updateSort($id, $sort);

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $task = NewYearTaskService::getInstance()->getTaskById($id);
        if (is_null($task)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前任务不存在');
        }
        $task->delete();
        return $this->success();
    }
}
