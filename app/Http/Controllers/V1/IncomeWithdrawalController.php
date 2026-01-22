<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Mall\Catering\CateringIncomeWithdrawalService;
use App\Services\Mall\Catering\CateringShopIncomeService;
use App\Services\Mall\Goods\GoodsIncomeWithdrawalService;
use App\Services\Mall\Goods\GoodsShopIncomeService;
use App\Services\Mall\Hotel\HotelIncomeWithdrawalService;
use App\Services\Mall\Hotel\HotelShopIncomeService;
use App\Services\Mall\Scenic\ScenicIncomeWithdrawalService;
use App\Services\Mall\Scenic\ScenicShopIncomeService;
use App\Services\SystemTodoService;
use App\Utils\CodeResponse;
use App\Utils\Enums\MerchantType;
use App\Utils\Enums\TodoEnums;
use App\Utils\Inputs\IncomeWithdrawalInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IncomeWithdrawalController extends Controller
{
    public function submit()
    {
        /** @var IncomeWithdrawalInput $input */
        $input = IncomeWithdrawalInput::new();

        $date = Carbon::now()->day;
        if ($date < 25) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '每月25-31号才可提现');
        }
        if ($input->amount == 0) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '提现金额不能为0');
        }

        $shopId = 0;
        switch ($input->merchantType) {
            case MerchantType::SCENIC:
                $shopId = $this->user()->scenicShop->id ?? 0;
                break;
            case MerchantType::HOTEL:
                $shopId = $this->user()->hotelShop->id ?? 0;
                break;
            case MerchantType::CATERING:
                $shopId = $this->user()->cateringShop->id ?? 0;
                break;
            case MerchantType::GOODS:
                $shopId = $this->user()->shop->id ?? 0;
                break;
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
                $withdrawAmount = GoodsShopIncomeService::getInstance()
                    ->getShopIncomeQuery($shopId, [2])
                    ->whereMonth('created_at', '!=', Carbon::now()->month)
                    ->sum('income_amount');
                break;
        }

        if (bccomp($withdrawAmount, $input->amount, 2) != 0) {
            $errMsg = "用户（ID：{$this->userId()}）店铺（type：{$input->merchantType}）收益提现金额（{$input->amount}）与实际可提现金额（{$withdrawAmount}）不一致，请检查";
            Log::error($errMsg);
            return $this->fail(CodeResponse::INVALID_OPERATION, "提现失败，请联系客服");
        }

        DB::transaction(function () use ($shopId, $withdrawAmount, $input) {
            switch ($input->merchantType) {
                case MerchantType::SCENIC:
                    $withdrawal = ScenicIncomeWithdrawalService::getInstance()
                        ->addWithdrawal($shopId, $this->userId(), $withdrawAmount, $input);
                    ScenicShopIncomeService::getInstance()->applyWithdrawal($shopId, $withdrawal->id);
                    SystemTodoService::getInstance()
                        ->createTodo(TodoEnums::SCENIC_INCOME_WITHDRAWAL_NOTICE, [$withdrawal->id]);
                    break;

                case MerchantType::HOTEL:
                    $withdrawal = HotelIncomeWithdrawalService::getInstance()
                        ->addWithdrawal($shopId, $this->userId(), $withdrawAmount, $input);
                    HotelShopIncomeService::getInstance()->applyWithdrawal($shopId, $withdrawal->id);
                    SystemTodoService::getInstance()
                        ->createTodo(TodoEnums::HOTEL_INCOME_WITHDRAWAL_NOTICE, [$withdrawal->id]);
                    break;

                case MerchantType::CATERING:
                    $withdrawal = CateringIncomeWithdrawalService::getInstance()
                        ->addWithdrawal($shopId, $this->userId(), $withdrawAmount, $input);
                    CateringShopIncomeService::getInstance()->applyWithdrawal($shopId, $withdrawal->id);
                    SystemTodoService::getInstance()
                        ->createTodo(TodoEnums::CATERING_INCOME_WITHDRAWAL_NOTICE, [$withdrawal->id]);
                    break;

                case MerchantType::GOODS:
                    $withdrawal = GoodsIncomeWithdrawalService::getInstance()
                        ->addWithdrawal($shopId, $this->userId(), $withdrawAmount, $input);
                    GoodsShopIncomeService::getInstance()->applyWithdrawal($shopId, $withdrawal->id);
                    SystemTodoService::getInstance()
                        ->createTodo(TodoEnums::GOODS_INCOME_WITHDRAWAL_NOTICE, [$withdrawal->id]);
                    break;
            }
            // todo 管理后台提现通知
        });

        return $this->success();
    }

    public function recordList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $merchantType = $this->verifyRequiredId('merchantType');
        $shopId = $this->verifyRequiredId('shopId');

        $page = collect();
        switch ($merchantType) {
            case MerchantType::SCENIC:
                $page = ScenicIncomeWithdrawalService::getInstance()->getShopPage($shopId, $input);
                break;

            case MerchantType::HOTEL:
                $page = HotelIncomeWithdrawalService::getInstance()->getShopPage($shopId, $input);
                break;

            case MerchantType::CATERING:
                $page = CateringIncomeWithdrawalService::getInstance()->getShopPage($shopId, $input);
                break;

            case MerchantType::GOODS:
                $page = GoodsIncomeWithdrawalService::getInstance()->getShopPage($shopId, $input);
                break;
        }

        return $this->successPaginate($page);
    }
}
