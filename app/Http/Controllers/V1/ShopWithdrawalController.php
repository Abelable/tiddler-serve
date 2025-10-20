<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\AccountService;
use App\Services\ShopIncomeService;
use App\Services\ShopWithdrawalService;
use App\Utils\CodeResponse;
use App\Utils\Enums\AccountChangeType;
use App\Utils\Enums\MerchantType;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\IncomeWithdrawalInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShopWithdrawalController extends Controller
{
    public function submit()
    {
        /** @var IncomeWithdrawalInput $input */
        $input = IncomeWithdrawalInput::new();
        $shopId = $this->verifyRequiredId('shopId');

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

        $withdrawAmount = ShopIncomeService::getInstance()
            ->getShopIncomeQuery($shopId, [2])
            ->whereMonth('created_at', '!=', Carbon::now()->month)
            ->sum('income_amount');

        if (bccomp($withdrawAmount, $input->withdrawAmount, 2) != 0) {
            $errMsg = "用户（ID：{$this->userId()}）提现店铺（ID：{$shopId}）收益金额（{$input->withdrawAmount}）与实际可提现金额（{$withdrawAmount}）不一致，请检查";
            Log::error($errMsg);
            return $this->fail(CodeResponse::INVALID_OPERATION, $errMsg);
        }

        DB::transaction(function () use ($shopId, $withdrawAmount, $input) {
            $withdrawal = ShopWithdrawalService::getInstance()
                ->addWithdrawal(MerchantType::GOODS, $shopId, $this->userId(), $withdrawAmount, $input);

            if ($input->path == 3) { // 提现至余额
                ShopIncomeService::getInstance()->finishWithdrawal($shopId, $withdrawal->id);
                AccountService::getInstance()
                    ->updateBalance($this->userId(), AccountChangeType::INCOME_WITHDRAWAL, $withdrawAmount);
            } else {
                ShopIncomeService::getInstance()->applyWithdrawal($shopId, $withdrawal->id);

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
        $shopId = $this->verifyRequiredId('shopId');

        $page = ShopWithdrawalService::getInstance()->getShopPage(MerchantType::GOODS, $shopId, $input);

        return $this->successPaginate($page);
    }
}
