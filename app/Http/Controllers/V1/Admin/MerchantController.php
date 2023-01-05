<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\MerchantOrderService;
use App\Services\MerchantService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\MerchantListInput;
use Illuminate\Support\Facades\DB;

class MerchantController extends Controller
{
    protected $guard = 'admin';

    public function list()
    {
        $input = MerchantListInput::new();
        $columns = ['id', 'type', 'status', 'failure_reason', 'name', 'mobile', 'created_at', 'updated_at'];
        $list = MerchantService::getInstance()->getMerchantList($input, $columns);
        return $this->successPaginate($list);
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

        DB::transaction(function () use ($merchant) {
            $merchant->status = 1;
            $merchant->save();

            MerchantOrderService::getInstance()->createMerchantOrder($merchant->user_id, $merchant->id, $merchant->type == 1 ? '1000' : '10000');
        });

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

        $merchant->status = 3;
        $merchant->failure_reason = $reason;
        $merchant->save();
        return $this->success();
    }
}
