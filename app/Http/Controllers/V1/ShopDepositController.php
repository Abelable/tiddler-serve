<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\ShopDepositChangeLogService;
use App\Services\ShopDepositService;
use App\Utils\Inputs\PageInput;

class ShopDepositController extends Controller
{
    public function accountInfo()
    {
        $shopId = $this->verifyRequiredId('shopId');

        $deposits = ShopDepositService::getInstance()->getShopDeposit($shopId);

        return $this->success([
            'id' => $deposits->id,
            'balance' => $deposits->balance ?: 0,
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
