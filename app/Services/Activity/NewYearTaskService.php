<?php

namespace App\Services\Activity;

use App\Models\Activity\NewYearTask;
use App\Services\BaseService;
use App\Utils\Inputs\Activity\NewYearTaskInput;
use App\Utils\Inputs\PageInput;

class NewYearTaskService extends BaseService
{
    public function getPage(PageInput $input, $columns = ['*'])
    {
        return NewYearTask::query()
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

    public function getTaskById($id, $columns = ['*'])
    {
        return NewYearTask::query()->find($id, $columns);
    }

    public function updateSort($id, $sort)
    {
        NewYearTask::query()->where('id', $id)->update(['sort' => $sort]);
    }
}
