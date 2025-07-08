<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\ScenicShopDepositChangeLogService;
use App\Services\ScenicShopDepositService;
use App\Services\ScenicShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use Yansongda\LaravelPay\Facades\Pay;

class ScenicShopDepositController extends Controller
{
    public function payParams()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $wxPayOrder = ScenicShopService::getInstance()
            ->createWxPayOrder($shopId, $this->userId(), $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($wxPayOrder);
        return $this->success($payParams);
    }

    public function depositInfo()
    {
        $shopId = $this->verifyRequiredId('shopId');

        $deposit = ScenicShopDepositService::getInstance()->getShopDeposit($shopId);
        if (is_null($deposit)) {
            return $this->fail(CodeResponse::NOT_FOUND, '保证金数据不存在');
        }

        $shopInfo = ScenicShopService::getInstance()->getShopById($shopId);
        $dueAmount = bcsub($shopInfo->deposit, $deposit->balance, 2);

        return $this->success([
            'balance' => $deposit->balance,
            'dueAmount' => $dueAmount
        ]);
    }

    public function changeLogList()
    {
        $shopId = $this->verifyRequiredId('shopId');
        /** @var PageInput $input */
        $input = PageInput::new();

        $page = ScenicShopDepositChangeLogService::getInstance()->getLogPage($shopId, $input);

        return $this->successPaginate($page);
    }
}
