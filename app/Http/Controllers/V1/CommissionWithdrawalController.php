<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\AccountService;
use App\Services\Mall\CommissionService;
use App\Services\Mall\CommissionWithdrawalService;
use App\Services\SystemTodoService;
use App\Utils\CodeResponse;
use App\Utils\Enums\AccountChangeType;
use App\Utils\Enums\TodoEnums;
use App\Utils\Inputs\CommissionWithdrawalInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommissionWithdrawalController extends Controller
{
    public function submit()
    {
        /** @var CommissionWithdrawalInput $input */
        $input = CommissionWithdrawalInput::new();

        if (is_null($this->user()->authInfo)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '需完成实名认证才可提现');
        }

        $date = Carbon::now()->day;
        if ($date < 25) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '每月25-31号才可提现');
        }

        if ($input->amount == 0) {
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

        if (bccomp($withdrawAmount, $input->amount, 2) != 0) {
            $errMsg = "用户（ID：{$this->userId()}）提现金额（{$input->amount}）与实际可提现金额（{$withdrawAmount}）不一致，请检查";
            Log::error($errMsg);
            return $this->fail(CodeResponse::INVALID_OPERATION, $errMsg);
        }

        DB::transaction(function () use ($withdrawAmount, $input) {
            $withdrawal = CommissionWithdrawalService::getInstance()->addWithdrawal($this->userId(), $withdrawAmount, $input);

            if ($input->path == 3) { // 提现至余额
                CommissionService::getInstance()->finishWithdrawal($this->userId(), $input->scene, $withdrawal->id);
                AccountService::getInstance()
                    ->updateBalance($this->userId(), AccountChangeType::COMMISSION_WITHDRAWAL, $withdrawAmount);
            } else {
                CommissionService::getInstance()->applyWithdrawal($this->userId(), $input->scene, $withdrawal->id);

                // todo 管理后台提现通知
                SystemTodoService::getInstance()->createTodo(TodoEnums::COMMISSION_WITHDRAWAL_NOTICE, [$withdrawal->id]);
            }
        });

        return $this->success();
    }

    public function recordList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = CommissionWithdrawalService::getInstance()->getUserRecordList($this->userId(), $input);
        return $this->successPaginate($page);
    }
}
