<?php

namespace App\Services\Activity;

use App\Models\Activity\NewYearTask;
use App\Services\BaseService;
use App\Services\RelationService;
use App\Utils\Inputs\Activity\NewYearTaskInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\Log;

class NewYearTaskService extends BaseService
{
    public function getPage(PageInput $input, $columns = ['*'])
    {
        return NewYearTask::query()
            ->orderByRaw('status = 1 DESC')
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
        $task->type = $input->type;
        $task->icon = $input->icon;
        $task->name = $input->name;
        $task->desc = $input->desc;
        $task->btn_content = $input->btnContent;
        $task->luck_score = $input->luckScore;
        $task->scene = $input->scene;
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

    public function finishInviteTask($superiorId)
    {
        $todayNewComerCount = RelationService::getInstance()->getTodayCountBySuperiorId($superiorId);
        $todayLuck = NewYearLuckService::getInstance()->getTodayLuckByUserId($superiorId, 0);

        if ($todayNewComerCount >= 4 && is_null($todayLuck)) {
            try {
                NewYearLuckService::getInstance()->createLuck($superiorId, '成功邀请新人助力', 1, 80);
                NewYearLuckService::getInstance()->updateUserLuck($superiorId, 80);
            } catch (\Exception $e) {
                // 23000 = MySQL 唯一约束冲突
                if ($e->getCode() === '23000') {
                    Log::warning('重复添加福气值（已被唯一索引拦截）', [
                        'user_id' => $superiorId,
                    ]);
                    return;
                }
                throw $e;
            }
        }
    }
}
