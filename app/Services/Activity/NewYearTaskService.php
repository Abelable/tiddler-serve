<?php

namespace App\Services\Activity;

use App\Models\Activity\NewYearTask;
use App\Services\BaseService;
use App\Utils\Inputs\Activity\NewYearTaskInput;

class NewYearTaskService extends BaseService
{
    public function addTask(NewYearTaskInput $input)
    {
        $task = NewYearTask::new();
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
}
