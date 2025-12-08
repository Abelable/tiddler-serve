<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\Mall\Catering\CateringMerchantService;
use App\Services\SystemTodoService;
use App\Utils\CodeResponse;
use App\Utils\Enums\TodoEnums;
use App\Utils\Inputs\MerchantPageInput;
use Illuminate\Support\Facades\DB;

class CateringMerchantController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var MerchantPageInput $input */
        $input = MerchantPageInput::new();
        $page = CateringMerchantService::getInstance()->getMerchantList($input);
        return $this->successPaginate($page);
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

        DB::transaction(function () use ($merchant) {
            $merchant->status = 1;
            $merchant->save();

            SystemTodoService::getInstance()->finishTodo(TodoEnums::CATERING_MERCHANT_NOTICE, $merchant->id);
            // todo：短信通知餐饮商家
        });

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

        DB::transaction(function () use ($reason, $merchant) {
            $merchant->status = 3;
            $merchant->failure_reason = $reason;
            $merchant->save();

            SystemTodoService::getInstance()->finishTodo(TodoEnums::CATERING_MERCHANT_NOTICE, $merchant->id);
            // todo：短信通知餐饮商家
        });

        return $this->success();
    }
}
