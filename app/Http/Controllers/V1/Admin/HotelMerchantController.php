<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\HotelMerchant;
use App\Services\HotelMerchantService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\HotelMerchantPageInput;

class HotelMerchantController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var HotelMerchantPageInput $input */
        $input = HotelMerchantPageInput::new();

        $page = HotelMerchantService::getInstance()->getMerchantList($input);

        $list = collect($page->items())->map(function (HotelMerchant $merchant) {
            $merchant['depositInfo'] = $merchant->depositInfo;
            return $merchant;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $merchant = HotelMerchantService::getInstance()->getMerchantById($id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店服务商不存在');
        }
        return $this->success($merchant);
    }

    public function approve()
    {
        $id = $this->verifyRequiredId('id');

        $merchant = HotelMerchantService::getInstance()->getMerchantById($id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店服务商不存在');
        }

        $merchant->status = 1;
        $merchant->save();

        // todo：短信通知酒店服务商

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $merchant = HotelMerchantService::getInstance()->getMerchantById($id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店服务商不存在');
        }

        $merchant->status = 3;
        $merchant->failure_reason = $reason;
        $merchant->save();

        // todo：短信通知酒店服务商

        return $this->success();
    }
}
