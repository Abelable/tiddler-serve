<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Activity\NewYearLuck;
use App\Models\Activity\NewYearTask;
use App\Services\Activity\NewYearGoodsService;
use App\Services\Activity\NewYearLuckService;
use App\Services\Activity\NewYearPrizeService;
use App\Services\Activity\NewYearTaskService;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class NewYearController extends Controller
{
    protected $except = ['prizeList', 'goodsList', 'finishTask'];

    public function prizeList()
    {
        $list = Cache::remember('new_year_prize_list', 10080, function () {
            return NewYearPrizeService::getInstance()->getList();
        });
        return $this->success($list);
    }

    public function goodsList()
    {
        $list = Cache::remember('new_year_goods_list', 10080, function () {
            return NewYearGoodsService::getInstance()->getList();
        });
        return $this->success($list);
    }

    public function taskList()
    {
        $taskList = Cache::remember('new_year_task_list', 10080, function () {
            return NewYearTaskService::getInstance()->getList();
        });
        $luckCountMap = NewYearLuckService::getInstance()->getLuckCountMap($this->userId());
        $luckList = NewYearLuckService::getInstance()->getUserLatestLuckList($this->userId());
        $avatar = $this->user()->avatar;

        $list = $taskList->map(function (NewYearTask $task) use ($luckCountMap, $luckList, $avatar) {
            /** @var NewYearLuck $luck */
            $luck = $luckList->get($task->id);
            $luckCount = $luckCountMap->get($task->id, 0);

            // 超次数限制
            if ($task->time_limit != 0 && $luckCount >= $task->time_limit) {
                if (!is_null($luck) && Carbon::parse($luck->created_at)->isToday()) {
                    $task['status'] = 2;
                    return $task;
                }

                return null;
            }

            // 特殊任务：设置头像昵称
            if ($task->id == 5) {
                if (!is_null($luck) && Carbon::parse($luck->created_at)->isToday()) {
                    $task['status'] = 2;
                    return $task;
                }
                if ($avatar && strpos($avatar, 'default_avatar') === false) {
                    return null;
                }
            }

            // 单次任务
            if (!is_null($luck) && $task->type == 1) {
                if (Carbon::parse($luck->created_at)->isToday()) {
                    $task['status'] = 2;
                    return $task;
                }

                return null;
            }

            // 每日任务
            if (!is_null($luck) && $task->type == 2 && Carbon::parse($luck->created_at)->isToday()) {
                $task['status'] = 2;
                return $task;
            }

            $task['status'] = 1;
            return $task;
        })->filter()->values();

        return $this->success($list);
    }

    public function finishTask()
    {
        $taskId = $this->verifyRequiredId('taskId');
        $userId = $this->verifyId('userId');
        $referenceId = $this->verifyString('referenceId', '');

        if (empty($userId)) {
            $userId = $this->userId();
        }

        NewYearTaskService::getInstance()->finishTask($userId, $taskId, $referenceId);

        return $this->success();
    }

    public function luckScore()
    {
        $luckScore = NewYearLuckService::getInstance()->getUserLuckScore($this->userId());
        return $this->success($luckScore);
    }

    public function luckList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        $columns = ['id', 'desc', 'type', 'score', 'created_at'];
        $page = NewYearLuckService::getInstance()->getUserLuckPage($this->userId(), $input, $columns);

        return $this->successPaginate($page);
    }
}
