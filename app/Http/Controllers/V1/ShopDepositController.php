<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Mall\Goods\ShopDepositChangeLogService;
use App\Services\Mall\Goods\ShopDepositService;
use App\Services\Mall\Goods\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use Yansongda\LaravelPay\Facades\Pay;

class ShopDepositController extends Controller
{
    public function payParams()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $wxPayOrder = ShopService::getInstance()
            ->createWxPayOrder($shopId, $this->userId(), $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($wxPayOrder);
        return $this->success($payParams);
    }

    public function depositInfo()
    {
        $shopId = $this->verifyRequiredId('shopId');

        $deposit = ShopDepositService::getInstance()->getShopDeposit($shopId);
        if (is_null($deposit)) {
            return $this->fail(CodeResponse::NOT_FOUND, '保证金数据不存在');
        }

        $shopInfo = ShopService::getInstance()->getShopById($shopId);
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

        $page = ShopDepositChangeLogService::getInstance()->getLogPage($shopId, $input);

        return $this->successPaginate($page);
    }
}
