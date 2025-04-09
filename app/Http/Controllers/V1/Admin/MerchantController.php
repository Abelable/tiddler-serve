<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\MerchantService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\MerchantPageInput;

class MerchantController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var MerchantPageInput $input */
        $input = MerchantPageInput::new();
        $page = MerchantService::getInstance()->getMerchantList($input);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $merchant = MerchantService::getInstance()->getMerchantById($id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商家不存在');
        }
        return $this->success($merchant);
    }

    public function approved()
    {
        $id = $this->verifyRequiredId('id');

        $merchant = MerchantService::getInstance()->getMerchantById($id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商家不存在');
        }

        $merchant->status = 1;
        $merchant->save();

        // todo：短信通知商家

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $merchant = MerchantService::getInstance()->getMerchantById($id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商家不存在');
        }

        $merchant->status = 2;
        $merchant->failure_reason = $reason;
        $merchant->save();

        // todo：短信通知商家

        return $this->success();
    }
}
