<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScenicMerchant;
use App\Services\ScenicMerchantService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ScenicMerchantListInput;

class ScenicMerchantController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var ScenicMerchantListInput $input */
        $input = ScenicMerchantListInput::new();

        $page = ScenicMerchantService::getInstance()->getMerchantList($input);

        $list = collect($page->items())->map(function (ScenicMerchant $merchant) {
            $merchant['depositInfo'] = $merchant->depositInfo;
            return $merchant;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $merchant = ScenicMerchantService::getInstance()->getMerchantById($id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景区服务商不存在');
        }
        return $this->success($merchant);
    }

    public function approved()
    {
        $id = $this->verifyRequiredId('id');

        $merchant = ScenicMerchantService::getInstance()->getMerchantById($id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景区服务商不存在');
        }

        $merchant->status = 1;
        $merchant->save();

        // todo：短信通知景区服务商

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $merchant = ScenicMerchantService::getInstance()->getMerchantById($id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景区服务商不存在');
        }

        $merchant->status = 3;
        $merchant->failure_reason = $reason;
        $merchant->save();

        // todo：短信通知景区服务商

        return $this->success();
    }
}
