<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\AccountChangeLogService;
use App\Services\AccountService;
use App\Utils\Inputs\PageInput;

class AccountController extends Controller
{
    protected $except = [];
    public function accountInfo()
    {
        $account = AccountService::getInstance()->getUserAccount($this->userId());
        return $this->success([
            'id' => $account->id,
            'balance' => $account->balance ?: 0,
        ]);
    }

    public function changeLogList()
    {
        $accountId = $this->verifyRequiredInteger('accountId');
        /** @var PageInput $input */
        $input = PageInput::new();

        $page = AccountChangeLogService::getInstance()->getLogPage($accountId, $input);

        return $this->successPaginate($page);
    }
}
