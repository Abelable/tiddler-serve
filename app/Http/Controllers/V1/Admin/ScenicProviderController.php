<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProviderScenicSpot;
use App\Models\ScenicProvider;
use App\Models\ScenicProviderOrder;
use App\Models\ScenicSpot;
use App\Services\ProviderScenicSpotService;
use App\Services\ScenicProviderOrderService;
use App\Services\ScenicProviderService;
use App\Services\ScenicService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\ProviderScenicListInput;
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
        $columns = ['id', 'provider_id', 'order_sn', 'payment_amount', 'status', 'pay_id', 'created_at', 'updated_at'];
        $page = ScenicProviderOrderService::getInstance()->getOrderList($input, $columns);
        $orderList = collect($page->items());
        $providerIds = $orderList->pluck('provider_id')->toArray();
        $providerList = ScenicProviderService::getInstance()->getProviderListByIds($providerIds, ['id', 'company_name'])->keyBy('id');
        $list = $orderList->map(function (ScenicProviderOrder $order) use ($providerList) {
            /** @var ScenicProvider $provider */
            $provider = $providerList->get($order->provider_id);
            $order['company_name'] = $provider->company_name;
            return $order;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function providerScenicList()
    {
        /** @var ProviderScenicListInput  $input */
        $input = ProviderScenicListInput::new();
        $page = ProviderScenicSpotService::getInstance()->getScenicList($input, ['id', 'scenic_id', 'provider_id', 'status', 'failure_reason', 'created_at', 'updated_at']);
        $providerScenicList = collect($page->items());

        $providerIds = $providerScenicList->pluck('provider_id')->toArray();
        $providerList = ScenicProviderService::getInstance()->getProviderListByIds($providerIds, ['id', 'company_name', 'business_license_photo'])->keyBy('id');

        $scenicIds = $providerScenicList->pluck('scenic_id')->toArray();
        $scenicList = ScenicService::getInstance()->getScenicListByIds($scenicIds, ['id', 'name', 'image_list'])->keyBy('id');

        $list = $providerScenicList->map(function (ProviderScenicSpot $providerScenic) use ($scenicList, $providerList) {
            /** @var ScenicProvider $provider */
            $provider = $providerList->get($providerScenic->provider_id);
            $providerScenic['provider_company_name'] = $provider->company_name;
            $providerScenic['provider_business_license_photo'] = $provider->business_license_photo;

            /** @var ScenicSpot $scenic */
            $scenic = $scenicList->get($providerScenic->scenic_id);
            $providerScenic['scenic_name'] = $scenic->name;
            $providerScenic['scenic_image'] = json_decode($scenic->image_list)[0];

            return $providerScenic;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function approvedScenicApply()
    {
        $id = $this->verifyRequiredId('id');

        $scenic = ProviderScenicSpotService::getInstance()->getScenicById($id);
        if (is_null($scenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前服务商景区不存在');
        }
        $scenic->status = 1;
        $scenic->save();

        return $this->success();
    }

    public function rejectScenicApply()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $scenic = ProviderScenicSpotService::getInstance()->getScenicById($id);
        if (is_null($scenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前服务商景区不存在');
        }
        $scenic->status = 2;
        $scenic->failure_reason = $reason;
        $scenic->save();

        return $this->success();
    }

    public function deleteScenicApply()
    {
        $id = $this->verifyRequiredId('id');

        $scenic = ProviderScenicSpotService::getInstance()->getScenicById($id);
        if (is_null($scenic)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前服务商景区不存在');
        }
        $scenic->delete();

        return $this->success();
    }
}
