<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\HotelMerchant;
use App\Services\HotelMerchantService;
use App\Services\SystemTodoService;
use App\Utils\CodeResponse;
use App\Utils\Enums\TodoEnums;
use App\Utils\Inputs\MerchantPageInput;
use Illuminate\Support\Facades\DB;

class HotelMerchantController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var MerchantPageInput $input */
        $input = MerchantPageInput::new();
        $page = HotelMerchantService::getInstance()->getMerchantList($input);
        return $this->successPaginate($page);
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

        DB::transaction(function () use ($merchant) {
            $merchant->status = 1;
            $merchant->save();

            SystemTodoService::getInstance()->finishTodo(TodoEnums::HOTEL_MERCHANT_NOTICE, $merchant->id);
            // todo 短信通知酒店服务商
        });

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

        DB::transaction(function () use ($reason, $merchant) {
            $merchant->status = 3;
            $merchant->failure_reason = $reason;
            $merchant->save();

            SystemTodoService::getInstance()->finishTodo(TodoEnums::HOTEL_MERCHANT_NOTICE, $merchant->id);
            // todo 短信通知酒店服务商
        });

        return $this->success();
    }
}
