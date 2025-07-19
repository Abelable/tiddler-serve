<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catering\CateringMerchant;
use App\Services\Mall\Catering\CateringMerchantService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\MerchantPageInput;

class CateringMerchantController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var MerchantPageInput $input */
        $input = MerchantPageInput::new();

        $page = CateringMerchantService::getInstance()->getMerchantList($input);

        $list = collect($page->items())->map(function (CateringMerchant $merchant) {
            $merchant['depositInfo'] = $merchant->depositInfo;
            return $merchant;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $merchant = CateringMerchantService::getInstance()->getMerchantById($id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮商家不存在');
        }
        return $this->success($merchant);
    }

    public function approve()
    {
        $id = $this->verifyRequiredId('id');

        $merchant = CateringMerchantService::getInstance()->getMerchantById($id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮商家不存在');
        }

        $merchant->status = 1;
        $merchant->save();

        // todo：短信通知餐饮商家

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $merchant = CateringMerchantService::getInstance()->getMerchantById($id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮商家不存在');
        }

        $merchant->status = 3;
        $merchant->failure_reason = $reason;
        $merchant->save();

        // todo：短信通知餐饮商家

        return $this->success();
    }
}
