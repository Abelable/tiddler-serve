<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Activity\NewYearGoodsService;
use App\Services\Activity\NewYearLuckService;
use App\Services\Activity\NewYearPrizeService;
use App\Services\Activity\NewYearTaskService;
use App\Utils\CodeResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class NewYearController extends Controller
{
    public function taskList()
    {
        $list = Cache::remember('new_year_task_list', 10080, function () {
            return NewYearTaskService::getInstance()->getList();
        });
        return $this->success($list);
    }

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

    public function finishTask()
    {
        $taskId = $this->verifyRequiredId('id');

        $task = NewYearTaskService::getInstance()->getTaskById($taskId);
        if (!$task || $task->status != 1) {
            return $this->fail(CodeResponse::NOT_FOUND, '任务不存在');
        }

        $userId = $this->userId();

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
                    $task->luck_score
                );

                NewYearLuckService::getInstance()->updateUserLuck(
                    $userId,
                    $task->luck_score
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
}
