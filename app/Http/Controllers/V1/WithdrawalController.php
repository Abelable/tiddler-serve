<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\AccountService;
use App\Services\CommissionService;
use App\Services\WithdrawalService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\WithdrawalInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawalController extends Controller
{
    public function submit()
    {
        /** @var WithdrawalInput $input */
        $input = WithdrawalInput::new();

        if (is_null($this->user()->authInfo)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '需完成实名认证才可提现');
        }

        $date = Carbon::now()->day;
        if ($date < 25) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '每月25-31号才可提现');
        }

        if ($input->withdrawAmount == 0) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '提现金额不能为0');
        }

        $withdrawAmount = 0;
        $commissionQuery = CommissionService::getInstance()
            ->getUserCommissionQuery([$this->userId()], [2])
            ->whereMonth('created_at', '!=', Carbon::now()->month);
        switch ($input->scene) {
            case 1:
                $withdrawAmount = $commissionQuery->where('scene', 1)->sum('commission_amount');
                break;

            case 2:
                $withdrawAmount = $commissionQuery->whereIn('scene', [2, 3])->sum('commission_amount');;
                break;

            case 3:
                $withdrawAmount = $commissionQuery->whereIn('scene', [4, 5])->sum('commission_amount');;
                break;
        }

        if (bccomp($withdrawAmount, $input->withdrawAmount, 2) != 0) {
            $errMsg = "用户（ID：{$this->userId()}）提现金额（{$input->withdrawAmount}）与实际可提现金额（{$withdrawAmount}）不一致，请检查";
            Log::error($errMsg);
            return $this->fail(CodeResponse::INVALID_OPERATION, $errMsg);
        }

        DB::transaction(function () use ($withdrawAmount, $input) {
            $withdrawal = WithdrawalService::getInstance()->addWithdrawal($this->userId(), $withdrawAmount, $input);

            if ($input->path == 3) { // 提现至余额
                CommissionService::getInstance()->settleCommissionToBalance($this->userId(), $input->scene, $withdrawal->id);
                AccountService::getInstance()->updateBalance($this->userId(), 1, $withdrawAmount);
            } else {
                CommissionService::getInstance()->withdrawUserCommission($this->userId(), $input->scene, $withdrawal->id);

                // todo 管理后台提现通知
                // AdminTodoService::getInstance()->createTodo(NotificationEnums::WITHDRAWAL_NOTICE, [$withdrawal->id]);
            }
        });

        return $this->success();
    }

    public function recordList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = WithdrawalService::getInstance()->getUserRecordList($this->userId(), $input);
        return $this->successPaginate($page);
    }
}
