<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Mall\Catering\CateringShopDepositChangeLogService;
use App\Services\Mall\Catering\CateringShopDepositService;
use App\Services\Mall\Catering\CateringShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use Yansongda\LaravelPay\Facades\Pay;

class CateringShopDepositController extends Controller
{
    public function payParams()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $wxPayOrder = CateringShopService::getInstance()
            ->createWxPayOrder($shopId, $this->userId(), $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($wxPayOrder);
        return $this->success($payParams);
    }

    public function depositInfo()
    {
        $shopId = $this->verifyRequiredId('shopId');

        $deposit = CateringShopDepositService::getInstance()->getShopDeposit($shopId);
        if (is_null($deposit)) {
            return $this->fail(CodeResponse::NOT_FOUND, '保证金数据不存在');
        }

        $shopInfo = CateringShopService::getInstance()->getShopById($shopId);
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

        $page = CateringShopDepositChangeLogService::getInstance()->getLogPage($shopId, $input);

        return $this->successPaginate($page);
    }
}
