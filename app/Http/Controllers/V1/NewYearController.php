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
use Illuminate\Support\Facades\DB;

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
        // 单次任务ID：1-逛家乡好物，4-加群，5-设置头像，6-逛首页，8-AI互动，9-逛景点，12-逛酒店，15-逛餐饮
        // 每日任务ID：2-分享好物，7-分享游记，10-分享景点，13-分享酒店，16-分享餐饮

        $taskList = Cache::remember('new_year_task_list', 10080, function () {
            return NewYearTaskService::getInstance()->getList();
        });

        $luckList = NewYearLuckService::getInstance()->getUserLuckList($this->userId())->keyBy('task_id');

        $list = $taskList->map(function (NewYearTask $task) use ($luckList) {
            /** @var NewYearLuck $luck */
            $luck = $luckList->get($task->id);

            if (!is_null($luck) && in_array($task->id, [1, 4, 5, 6, 8, 9, 12, 15])) {
                if (Carbon::parse($luck->created_at)->isToday()) {
                    $task['status'] = 2;
                    return $task;
                }

                return null;
            }

            if (!is_null($luck) && in_array($task->id, [2, 7, 10, 13, 16])) {
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

        try {
            DB::transaction(function () use ($userId, $task) {
                NewYearLuckService::getInstance()->createLuck(
                    $userId,
                    $task->name,
                    1,
                    $task->luck_score,
                    $task->id,
                    $task->type,
                );
            });
        } catch (\Throwable $e) {
            if ((int)$e->getCode() === 23000) {
                return $this->success();
            }
            throw $e;
        }

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
