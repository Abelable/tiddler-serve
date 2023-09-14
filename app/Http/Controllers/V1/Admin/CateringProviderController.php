<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\CateringProvider;
use App\Models\CateringProviderOrder;
use App\Services\CateringProviderOrderService;
use App\Services\CateringProviderService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CateringProviderListInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

class CateringProviderController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        $input = CateringProviderListInput::new();
        $columns = ['id', 'type', 'status', 'failure_reason', 'name', 'mobile', 'created_at', 'updated_at'];

        $page = CateringProviderService::getInstance()->getProviderList($input, $columns);
        $providerList = collect($page->items());
        $providerIds = $providerList->pluck('id')->toArray();
        $providerOrderList = CateringProviderOrderService::getInstance()->getOrderListByProviderIds($providerIds)->keyBy('provider_id');
        $list = $providerList->map(function (CateringProvider $provider) use ($providerOrderList) {
            /** @var CateringProviderOrder $providerOrder */
            $depositInfo = $providerOrderList->get($provider->id);
            if (!is_null($depositInfo)) {
                unset($depositInfo->id);
                unset($depositInfo->user_id);
                unset($depositInfo->provider_id);
            }
            $provider['depositInfo'] = $depositInfo;

            return $provider;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $provider = CateringProviderService::getInstance()->getProviderById($id);
        if (is_null($provider)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商家不存在');
        }
        return $this->success($provider);
    }

    public function approved()
    {
        $id = $this->verifyRequiredId('id');

        $provider = CateringProviderService::getInstance()->getProviderById($id);
        if (is_null($provider)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商家不存在');
        }

        DB::transaction(function () use ($provider) {
            CateringProviderOrderService::getInstance()->createOrder($provider->user_id, $provider->id, $provider->type == 1 ? '1000' : '10000');
            $provider->status = 1;
            $provider->save();
        });

        // todo：短信通知商家

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $provider = CateringProviderService::getInstance()->getProviderById($id);
        if (is_null($provider)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商家不存在');
        }

        $provider->status = 3;
        $provider->failure_reason = $reason;
        $provider->save();
        // todo：短信通知商家

        return $this->success();
    }

    public function orderList()
    {
        $input = PageInput::new();
        $columns = ['id', 'provider_id', 'order_sn', 'payment_amount', 'status', 'pay_id', 'created_at', 'updated_at'];

        $page = CateringProviderOrderService::getInstance()->getOrderList($input, $columns);
        $orderList = collect($page->items());
        $providerIds = $orderList->pluck('provider_id')->toArray();
        $providerList = CateringProviderService::getInstance()->getProviderListByIds($providerIds, ['id', 'type', 'company_name', 'name'])->keyBy('id');
        $list = $orderList->map(function (CateringProviderOrder $order) use ($providerList) {
            /** @var CateringProvider $provider */
            $provider = $providerList->get($order->provider_id);
            $order['provider_type'] = $provider->type;
            if ($provider->type == 1) {
                $order['name'] = $provider->name;
            } else {
                $order['company_name'] = $provider->company_name;
            }
            return $order;
        });
        return $this->success($this->paginate($page, $list));
    }
}
