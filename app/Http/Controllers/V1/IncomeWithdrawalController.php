<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\AccountService;
use App\Services\HotelShopIncomeService;
use App\Services\Mall\Catering\CateringShopIncomeService;
use App\Services\ScenicShopIncomeService;
use App\Services\ShopIncomeService;
use App\Services\IncomeWithdrawalService;
use App\Utils\CodeResponse;
use App\Utils\Enums\AccountChangeType;
use App\Utils\Enums\MerchantType;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\IncomeWithdrawalInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IncomeWithdrawalController extends Controller
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

        if ($input->amount == 0) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '提现金额不能为0');
        }

        $withdrawAmount = 0;
        switch ($input->merchantType) {
            case MerchantType::SCENIC:
                $withdrawAmount = ScenicShopIncomeService::getInstance()
                    ->getShopIncomeQuery($shopId, [2])
                    ->whereMonth('created_at', '!=', Carbon::now()->month)
                    ->sum('income_amount');
                break;
            case MerchantType::HOTEL:
                $withdrawAmount = HotelShopIncomeService::getInstance()
                    ->getShopIncomeQuery($shopId, [2])
                    ->whereMonth('created_at', '!=', Carbon::now()->month)
                    ->sum('income_amount');
                break;
            case MerchantType::CATERING:
                $withdrawAmount = CateringShopIncomeService::getInstance()
                    ->getShopIncomeQuery($shopId, [2])
                    ->whereMonth('created_at', '!=', Carbon::now()->month)
                    ->sum('income_amount');
                break;
            case MerchantType::GOODS:
                $withdrawAmount = ShopIncomeService::getInstance()
                    ->getShopIncomeQuery($shopId, [2])
                    ->whereMonth('created_at', '!=', Carbon::now()->month)
                    ->sum('income_amount');
                break;
        }

        if (bccomp($withdrawAmount, $input->amount, 2) != 0) {
            $errMsg = "用户（ID：{$this->userId()}）提现店铺（ID：{$shopId}）收益金额（{$input->amount}）与实际可提现金额（{$withdrawAmount}）不一致，请检查";
            Log::error($errMsg);
            return $this->fail(CodeResponse::INVALID_OPERATION, $errMsg);
        }

        DB::transaction(function () use ($shopId, $withdrawAmount, $input) {
            $withdrawal = IncomeWithdrawalService::getInstance()
                ->addWithdrawal($input->merchantType, $shopId, $this->userId(), $withdrawAmount, $input);

            if ($input->path == 3) { // 提现至余额
                switch ($input->merchantType) {
                    case MerchantType::SCENIC:
                        ScenicShopIncomeService::getInstance()->finishWithdrawal($shopId, $withdrawal->id);
                        break;
                    case MerchantType::HOTEL:
                        HotelShopIncomeService::getInstance()->finishWithdrawal($shopId, $withdrawal->id);
                        break;
                    case MerchantType::CATERING:
                        CateringShopIncomeService::getInstance()->finishWithdrawal($shopId, $withdrawal->id);
                        break;
                    case MerchantType::GOODS:
                        ShopIncomeService::getInstance()->finishWithdrawal($shopId, $withdrawal->id);
                        break;
                }

                AccountService::getInstance()
                    ->updateBalance($this->userId(), AccountChangeType::INCOME_WITHDRAWAL, $withdrawAmount);
            } else {
                switch ($input->merchantType) {
                    case MerchantType::SCENIC:
                        ScenicShopIncomeService::getInstance()->applyWithdrawal($shopId, $withdrawal->id);
                        break;
                    case MerchantType::HOTEL:
                        HotelShopIncomeService::getInstance()->applyWithdrawal($shopId, $withdrawal->id);
                        break;
                    case MerchantType::CATERING:
                        CateringShopIncomeService::getInstance()->applyWithdrawal($shopId, $withdrawal->id);
                        break;
                    case MerchantType::GOODS:
                        ShopIncomeService::getInstance()->applyWithdrawal($shopId, $withdrawal->id);
                        break;
                }

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
        $merchantType = $this->verifyRequiredId('merchantType');
        $shopId = $this->verifyRequiredId('shopId');

        $page = IncomeWithdrawalService::getInstance()->getShopPage($merchantType, $shopId, $input);

        return $this->successPaginate($page);
    }
}
