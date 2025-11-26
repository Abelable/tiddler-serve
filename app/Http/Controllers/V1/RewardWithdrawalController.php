<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\AccountService;
use App\Services\RewardWithdrawalService;
use App\Services\SystemTodoService;
use App\Services\TaskService;
use App\Services\UserTaskService;
use App\Utils\CodeResponse;
use App\Utils\Enums\AccountChangeType;
use App\Utils\Enums\TodoEnums;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\RewardWithdrawalInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RewardWithdrawalController extends Controller
{
    public function submit()
    {
        /** @var RewardWithdrawalInput $input */
        $input = RewardWithdrawalInput::new();

        if (is_null($this->user()->authInfo)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '需完成实名认证才可提现');
        }

        $task = TaskService::getInstance()->getTaskByStatus($input->taskId, [3]);
        if (is_null($task)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '任务不存在，无法领取奖励');
        }

        if (bccomp($task->reward_total, $input->amount, 2) != 0) {
            $errMsg = "用户（ID：{$this->userId()}）奖励金额（{$input->amount}）与实际可领取金额（{$task->reward_total}）不一致，请检查";
            Log::error($errMsg);
            return $this->fail(CodeResponse::INVALID_OPERATION, $errMsg);
        }

        $userTask = UserTaskService::getInstance()->getUserTaskByStatus($this->userId(), $input->taskId, [2]);
        if (is_null($userTask)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '任务不存在，无法领取奖励');
        }

        if (Carbon::now()->diffInDays(Carbon::parse($userTask->finish_time)) < 14) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '订单需保持14天，才可领取奖励');
        }

        DB::transaction(function () use ($userTask, $input) {
            $withdrawal = RewardWithdrawalService::getInstance()->addWithdrawal($this->userId(), $input);

            if ($input->path == 3) { // 提现至余额
                $userTask->status = 3;
                $userTask->withdrawal_id = $withdrawal->id;
                $userTask->save();

                AccountService::getInstance()
                    ->updateBalance($this->userId(), AccountChangeType::REWARD_WITHDRAWAL, $input->amount);
            } else {
                $userTask->status = 4;
                $userTask->withdrawal_id = $withdrawal->id;
                $userTask->save();

                // todo 管理后台提现通知
                SystemTodoService::getInstance()->createTodo(TodoEnums::WITHDRAWAL_NOTICE, [$withdrawal->id]);
            }
        });

        return $this->success();
    }

    public function recordList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = RewardWithdrawalService::getInstance()->getUserRecordList($this->userId(), $input);
        return $this->successPaginate($page);
    }
}
