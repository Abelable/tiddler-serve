<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ProviderHotel;
use App\Models\Hotel;
use App\Services\ProviderHotelService;
use App\Services\HotelProviderOrderService;
use App\Services\HotelProviderService;
use App\Services\HotelService;
use App\Services\HotelShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\HotelProviderInput;
use App\Utils\Inputs\StatusPageInput;
use Illuminate\Support\Facades\DB;
use Yansongda\LaravelPay\Facades\Pay;

class HotelProviderController extends Controller
{
    public function settleIn()
    {
        /** @var HotelProviderInput $input */
        $input = HotelProviderInput::new();

        $provider = HotelProviderService::getInstance()->getProviderByUserId($this->userId());
        if (!is_null($provider)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '您已提交申请，请勿重复操作');
        }

        DB::transaction(function () use ($input) {
            $provider = HotelProviderService::getInstance()->createProvider($input, $this->userId());
            HotelShopService::getInstance()->createShop($this->userId(), $provider->id, $input);
        });

        return $this->success();
    }

    public function statusInfo()
    {
        $provider = HotelProviderService::getInstance()->getProviderByUserId($this->userId(), ['id', 'status', 'failure_reason']);
        $providerOrder = HotelProviderOrderService::getInstance()->getOrderByUserId($this->userId(), ['id']);

        return $this->success($provider ? [
            'id' => $provider->id,
            'status' => $provider->status,
            'failureReason' => $provider->failure_reason,
            'orderId' => $providerOrder ? $providerOrder->id : 0
        ] : null);
    }

    public function payDeposit()
    {
        $orderId = $this->verifyRequiredId('orderId');
        $order = HotelProviderOrderService::getInstance()->getWxPayOrder($this->userId(), $orderId, $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($order);
        return $this->success($payParams);
    }

    public function deleteProvider()
    {
        $provider = HotelProviderService::getInstance()->getProviderByUserId($this->userId());
        if (is_null($provider)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景区服务商信息不存在');
        }
        $provider->delete();
        return $this->success();
    }

    public function myShopInfo()
    {
        $columns = ['id', 'name', 'type', 'avatar', 'cover'];
        $shop = HotelShopService::getInstance()->getShopByUserId($this->userId(), $columns);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
        }
        return $this->success($shop);
    }

    public function hotelListTotals()
    {
        return $this->success([
            ProviderHotelService::getInstance()->getListTotal($this->userId(), 0),
            ProviderHotelService::getInstance()->getListTotal($this->userId(), 1),
            ProviderHotelService::getInstance()->getListTotal($this->userId(), 2),
        ]);
    }

    public function providerHotelList()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();

        $page = ProviderHotelService::getInstance()->getUserHotelList($this->userId(), $input, ['id', 'hotel_id', 'status', 'failure_reason', 'created_at', 'updated_at']);
        $providerHotelList = collect($page->items());
        $hotelIds = $providerHotelList->pluck('hotel_id')->toArray();
        $hotelList = HotelService::getInstance()->getHotelListByIds($hotelIds, ['id', 'name', 'image_list', 'level', 'address'])->keyBy('id');
        $list = $providerHotelList->map(function (ProviderHotel $providerHotel) use ($hotelList) {
            /** @var Hotel $hotel */
            $hotel = $hotelList->get($providerHotel->hotel_id);
            $providerHotel['hotel_image'] = json_decode($hotel->image_list)[0];
            $providerHotel['hotel_name'] = $hotel->name;
            $providerHotel['hotel_level'] = $hotel->level;
            $providerHotel['hotel_address'] = $hotel->address;
            return $providerHotel;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function applyHotel()
    {
        $hotelId = $this->verifyRequiredId('hotelId');

        if (is_null($this->user()->hotelShop)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '暂无权限申请添加景点');
        }

        $hotel = HotelService::getInstance()->getHotelById($hotelId);
        if (is_null($hotel)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }

        $providerHotel = ProviderHotelService::getInstance()->getHotelByHotelId($this->userId(), $hotelId);
        if (!is_null($providerHotel)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '您已添加过当前景点');
        }

        $providerHotel = ProviderHotel::new();
        $providerHotel->user_id = $this->userId();
        $providerHotel->provider_id = $this->user()->hotelProvider->id;
        $providerHotel->hotel_id = $hotelId;
        $providerHotel->save();
        return $this->success();
    }

    public function deleteProviderHotel()
    {
        $id = $this->verifyRequiredId('id');
        $hotel = ProviderHotelService::getInstance()->getUserHotelById($this->userId(), $id);
        if (is_null($hotel)) {
            return $this->fail(CodeResponse::NOT_FOUND, '供应商景点不存在');
        }
        $hotel->delete();
        return $this->success();
    }

    public function providerHotelOptions()
    {
        $hotelIds = ProviderHotelService::getInstance()->getUserHotelOptions($this->userId())->pluck('hotel_id')->toArray();
        $hotelOptions = HotelService::getInstance()->getHotelListByIds($hotelIds, ['id', 'name']);
        return $this->success($hotelOptions);
    }
}
