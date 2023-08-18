<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProviderHotel;
use App\Models\HotelProvider;
use App\Models\HotelProviderOrder;
use App\Models\Hotel;
use App\Services\ProviderHotelService;
use App\Services\HotelProviderOrderService;
use App\Services\HotelProviderService;
use App\Services\HotelService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\ProviderHotelListInput;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\HotelProviderListInput;
use Illuminate\Support\Facades\DB;

class HotelProviderController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        $input = HotelProviderListInput::new();
        $columns = ['id', 'status', 'failure_reason', 'company_name', 'name', 'mobile', 'created_at', 'updated_at'];
        $page = HotelProviderService::getInstance()->getProviderList($input, $columns);
        $providerList = collect($page->items());
        $providerIds = $providerList->pluck('id')->toArray();
        $providerOrderList = HotelProviderOrderService::getInstance()->getOrderListByProviderIds($providerIds, ['id', 'provider_id'])->keyBy('provider_id');
        $list = $providerList->map(function (HotelProvider $provider) use ($providerOrderList) {
            /** @var HotelProviderOrder $providerOrder */
            $providerOrder = $providerOrderList->get($provider->id);
            $provider['order_id'] = $providerOrder ? $providerOrder->id : 0;
            return $provider;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $provider = HotelProviderService::getInstance()->getProviderById($id);
        if (is_null($provider)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景区服务商不存在');
        }
        return $this->success($provider);
    }

    public function approved()
    {
        $id = $this->verifyRequiredId('id');

        $provider = HotelProviderService::getInstance()->getProviderById($id);
        if (is_null($provider)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景区服务商不存在');
        }

        DB::transaction(function () use ($provider) {
            HotelProviderOrderService::getInstance()->createOrder($provider->user_id, $provider->id, '10000');
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

        $provider = HotelProviderService::getInstance()->getProviderById($id);
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
        $page = HotelProviderOrderService::getInstance()->getOrderList($input, $columns);
        $orderList = collect($page->items());
        $providerIds = $orderList->pluck('provider_id')->toArray();
        $providerList = HotelProviderService::getInstance()->getProviderListByIds($providerIds, ['id', 'company_name'])->keyBy('id');
        $list = $orderList->map(function (HotelProviderOrder $order) use ($providerList) {
            /** @var HotelProvider $provider */
            $provider = $providerList->get($order->provider_id);
            $order['company_name'] = $provider->company_name;
            return $order;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function providerHotelList()
    {
        /** @var ProviderHotelListInput  $input */
        $input = ProviderHotelListInput::new();
        $page = ProviderHotelService::getInstance()->getHotelList($input, ['id', 'hotel_id', 'provider_id', 'status', 'failure_reason', 'created_at', 'updated_at']);
        $providerHotelList = collect($page->items());

        $providerIds = $providerHotelList->pluck('provider_id')->toArray();
        $providerList = HotelProviderService::getInstance()->getProviderListByIds($providerIds, ['id', 'company_name', 'business_license_photo'])->keyBy('id');

        $hotelIds = $providerHotelList->pluck('hotel_id')->toArray();
        $hotelList = HotelService::getInstance()->getHotelListByIds($hotelIds, ['id', 'name', 'cover'])->keyBy('id');

        $list = $providerHotelList->map(function (ProviderHotel $providerHotel) use ($hotelList, $providerList) {
            /** @var HotelProvider $provider */
            $provider = $providerList->get($providerHotel->provider_id);
            $providerHotel['provider_company_name'] = $provider->company_name;
            $providerHotel['provider_business_license_photo'] = $provider->business_license_photo;

            /** @var Hotel $hotel */
            $hotel = $hotelList->get($providerHotel->hotel_id);
            $providerHotel['hotel_name'] = $hotel->name;
            $providerHotel['hotel_image'] = $hotel->cover;

            return $providerHotel;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function approvedHotelApply()
    {
        $id = $this->verifyRequiredId('id');

        $hotel = ProviderHotelService::getInstance()->getHotelById($id);
        if (is_null($hotel)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前服务商景区不存在');
        }
        $hotel->status = 1;
        $hotel->save();

        return $this->success();
    }

    public function rejectHotelApply()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $hotel = ProviderHotelService::getInstance()->getHotelById($id);
        if (is_null($hotel)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前服务商景区不存在');
        }
        $hotel->status = 2;
        $hotel->failure_reason = $reason;
        $hotel->save();

        return $this->success();
    }

    public function deleteHotelApply()
    {
        $id = $this->verifyRequiredId('id');

        $hotel = ProviderHotelService::getInstance()->getHotelById($id);
        if (is_null($hotel)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前服务商景区不存在');
        }
        $hotel->delete();

        return $this->success();
    }
}
