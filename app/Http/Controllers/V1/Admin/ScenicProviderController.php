<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScenicProvider;
use App\Models\ScenicProviderOrder;
use App\Services\ScenicProviderOrderService;
use App\Services\ScenicProviderService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\ScenicProviderListInput;
use Illuminate\Support\Facades\DB;

class ScenicProviderController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        $input = ScenicProviderListInput::new();
        $columns = ['id', 'status', 'failure_reason', 'company_name', 'name', 'mobile', 'created_at', 'updated_at'];
        $page = ScenicProviderService::getInstance()->getProviderList($input, $columns);
        $providerList = collect($page->items());
        $providerIds = $providerList->pluck('id')->toArray();
        $providerOrderList = ScenicProviderOrderService::getInstance()->getOrderListByProviderIds($providerIds, ['id', 'provider_id'])->keyBy('provider_id');
        $list = $providerList->map(function (ScenicProvider $provider) use ($providerOrderList) {
            /** @var ScenicProviderOrder $providerOrder */
            $providerOrder = $providerOrderList->get($provider->id);
            $provider['order_id'] = $providerOrder->id;
            return $provider;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $provider = ScenicProviderService::getInstance()->getProviderById($id);
        if (is_null($provider)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景区服务商不存在');
        }
        return $this->success($provider);
    }

    public function approved()
    {
        $id = $this->verifyRequiredId('id');

        $provider = ScenicProviderService::getInstance()->getProviderById($id);
        if (is_null($provider)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景区服务商不存在');
        }

        DB::transaction(function () use ($provider) {
            ScenicProviderOrderService::getInstance()->createOrder($provider->user_id, $provider->id, '10000');
            $provider->status = 1;
            $provider->save();
        });

        // todo：短信通知景区服务商

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $provider = ScenicProviderService::getInstance()->getProviderById($id);
        if (is_null($provider)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景区服务商不存在');
        }

        $provider->status = 3;
        $provider->failure_reason = $reason;
        $provider->save();
        // todo：短信通知景区服务商

        return $this->success();
    }

    public function orderList()
    {
        $input = PageInput::new();
        $columns = ['id', 'order_sn', 'payment_amount', 'status', 'pay_id', 'created_at', 'updated_at'];
        $list = ScenicProviderOrderService::getInstance()->getOrderList($input, $columns);
        return $this->successPaginate($list);
    }
}
