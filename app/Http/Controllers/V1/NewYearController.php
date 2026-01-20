<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Activity\NewYearTask;
use App\Services\Activity\NewYearDrawLogService;
use App\Services\Activity\NewYearGoodsService;
use App\Services\Activity\NewYearLuckService;
use App\Services\Activity\NewYearPrizeService;
use App\Services\Activity\NewYearTaskService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

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
        $luckListGroup = NewYearLuckService::getInstance()->getUserLatestLuckList($this->userId());
        $avatar = $this->user()->avatar;

        $list = $taskList->map(function (NewYearTask $task) use ($luckCountMap, $luckListGroup, $avatar) {
            $luckList = $luckListGroup->get($task->id, collect());
            $luck = $luckList->last();
            $luckCount = $luckCountMap->get($task->id, 0);

            // 超次数限制
            if ($task->time_limit != 0 && $luckCount >= $task->time_limit) {
                if (!is_null($luck) && Carbon::parse($luck->created_at)->isToday()) {
                    $task['status'] = 2;
                    return $task;
                }

                return null;
            }

            // 分享 - 如果有3次记录都是分享给同一个人，提前结束任务
            if (in_array($task->id, [2, 7, 10, 13, 16])) {
                $referenceCounts = $luckList->countBy('reference_id');
                if ($referenceCounts->contains(function ($count) {
                    return $count >= 3;
                })) {
                    if (!is_null($luck) && Carbon::parse($luck->created_at)->isToday()) {
                        $task['status'] = 2;
                        return $task;
                    }

                    return null;
                }
            }

            // 设置头像昵称
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

    public function draw()
    {
        $luckScore = NewYearLuckService::getInstance()->getUserLuckScore($this->userId());
        if ($luckScore < 20) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '福气值不足，无法抽奖');
        }

        $drawPrizeList = NewYearPrizeService::getInstance()->getDrawList($this->userId());

        $hitPrize = DB::transaction(function () use ($drawPrizeList) {
            NewYearLuckService::getInstance()
                ->createLuck($this->userId(), '福气抽奖', 2, -20, 0, 3);

            // 没有抽奖列表
            if (count($drawPrizeList) == 0) {
                NewYearDrawLogService::getInstance()->createLog($this->userId());
                return null;
            }

            // 累计分布
            $rand = mt_rand()/mt_getrandmax();
            $hitPrize = collect($drawPrizeList)->first(function($prize) use ($rand) {
                return $rand <= $prize->cumulative_rate;
            });

            // 未中奖
            if (is_null($hitPrize)) {
                NewYearDrawLogService::getInstance()->createLog($this->userId());
                return null;
            }

            // 库存检查（并发场景必要）
            if ($hitPrize->stock != -1) {
                $success = NewYearPrizeService::getInstance()->decreaseStock($hitPrize->id);
                if (!$success) {
                    // 库存不足降级奖品
                    $hitPrize = collect($drawPrizeList)->firstWhere('id', $hitPrize->fallback_prize_id) ?? null;
                    if (is_null($hitPrize)) {
                        NewYearDrawLogService::getInstance()->createLog($this->userId());
                        return null;
                    }
                }
            }

            // 成本熔断（Redis 原子累加）
            //            $totalCostKey = 'new_year:total_cost';
            //            $hitCost = (int) ($hitPrize->cost ?? 0);
            //            Cache::add($totalCostKey, 0, 86400); // 1天
            //            $newTotal = Cache::increment($totalCostKey, $hitCost);
            //            if ($newTotal > 300) {
            //                Cache::decrement($totalCostKey, $hitCost);
            //
            //                NewYearDrawLogService::getInstance()->createLog($this->userId());
            //                return null;
            //            }

            // 成本熔断（Lua脚本 原子性控制）
            $totalCostKey = 'new_year:total_cost';
            $hitCost = (int) ($hitPrize->cost ?? 0);
            $maxTotal = 300;

            // Lua 脚本
            $lua = <<<LUA
            local current = redis.call("GET", KEYS[1])
            if not current then
                current = 0
            end
            current = tonumber(current)
            local hitCost = tonumber(ARGV[1])
            local maxTotal = tonumber(ARGV[2])
            if current + hitCost > maxTotal then
                return -1
            else
                local newTotal = redis.call("INCRBY", KEYS[1], hitCost)
                redis.call("EXPIRE", KEYS[1], 86400)
                return newTotal
            end
            LUA;

            $newTotal = Redis::eval($lua, 1, $totalCostKey, $hitCost, $maxTotal);
            if ($newTotal === -1) {
                // 超额处理
                NewYearDrawLogService::getInstance()->createLog($this->userId());
                return null;
            }

            // 中奖记录
            if ($hitPrize->type == 1) {
                NewYearLuckService::getInstance()
                    ->createLuck($this->userId(), '抽奖获得福气值', 1, $hitPrize->luck_score, 0, 3);
            } else {
                NewYearPrizeService::getInstance()->createUserPrize($this->userId(), $hitPrize);
            }

            NewYearDrawLogService::getInstance()->createLog($this->userId(), $hitPrize);
            return $hitPrize;
        });

        return $this->success($hitPrize->id ?? 0);
    }

    public function userPrizeList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $columns = ['id', 'status', 'prize_id', 'prize_type', 'cover', 'name', 'coupon_id', 'goods_id', 'created_at'];
        $page = NewYearPrizeService::getInstance()->getUserPrizePage($this->userId(), $input, $columns);
        return $this->successPaginate($page);
    }
}
