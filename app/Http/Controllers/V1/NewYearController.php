<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Activity\NewYearLuck;
use App\Models\Activity\NewYearTask;
use App\Services\Activity\NewYearGoodsService;
use App\Services\Activity\NewYearLuckService;
use App\Services\Activity\NewYearPrizeService;
use App\Services\Activity\NewYearTaskService;
use App\Utils\CodeResponse;
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

        $avatar = $this->user()->avatar;

        $luckList = NewYearLuckService::getInstance()->getUserLuckList($this->userId())->keyBy('task_id');

        $list = $taskList->map(function (NewYearTask $task) use ($avatar, $luckList) {
            if ($task->id == 5 && $avatar && strpos($avatar, 'default_avatar') === false) {
                return null;
            }

            /** @var NewYearLuck $luck */
            $luck = $luckList->get($task->id);

            if (!is_null($luck) && $task->type == 1) {
                if (Carbon::parse($luck->created_at)->isToday()) {
                    $task['status'] = 2;
                    return $task;
                }

                return null;
            }

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

        $task = NewYearTaskService::getInstance()->getTaskById($taskId);
        if (!$task || $task->status != 1) {
            return $this->fail(CodeResponse::NOT_FOUND, '任务不存在');
        }

        $luck = NewYearLuckService::getInstance()->getLuckByUserId($userId, $taskId);
        $todayLuck = NewYearLuckService::getInstance()->getTodayLuckByUserId($userId, $taskId);

        $shouldGrant = false;

        if ($task->type == 1) {
            $shouldGrant = is_null($luck);
        } elseif ($task->type == 2) {
            $shouldGrant = is_null($todayLuck);
        }

        if (!$shouldGrant) {
            return $this->success(); // 幂等
        }

        NewYearLuckService::getInstance()->createLuck(
            $userId,
            $task->name,
            1,
            $task->luck_score,
            $task->id,
            $task->type,
            $referenceId
        );

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
