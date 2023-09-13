<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ProviderRestaurant;
use App\Models\Restaurant;
use App\Services\CateringProviderOrderService;
use App\Services\CateringProviderService;
use App\Services\RestaurantService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CateringProviderInput;
use App\Utils\Inputs\StatusPageInput;
use Illuminate\Support\Facades\DB;
use Yansongda\LaravelPay\Facades\Pay;

class CateringProviderController extends Controller
{
    public function settleIn()
    {
        /** @var CateringProviderInput $input */
        $input = CateringProviderInput::new();

        $provider = CateringProviderService::getInstance()->getProviderByUserId($this->userId());
        if (!is_null($provider)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '您已提交申请，请勿重复操作');
        }

        DB::transaction(function () use ($input) {
            $provider = CateringProviderService::getInstance()->createProvider($input, $this->userId());
            RestaurantService::getInstance()->createShop($this->userId(), $provider->id, $input);
        });

        return $this->success();
    }

    public function statusInfo()
    {
        $provider = CateringProviderService::getInstance()->getProviderByUserId($this->userId(), ['id', 'status', 'failure_reason']);
        $providerOrder = CateringProviderOrderService::getInstance()->getOrderByUserId($this->userId(), ['id']);

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
        $order = CateringProviderOrderService::getInstance()->getWxPayOrder($this->userId(), $orderId, $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($order);
        return $this->success($payParams);
    }

    public function deleteProvider()
    {
        $provider = CateringProviderService::getInstance()->getProviderByUserId($this->userId());
        if (is_null($provider)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景区服务商信息不存在');
        }
        $provider->delete();
        return $this->success();
    }

    public function myShopInfo()
    {
        $columns = ['id', 'name', 'type', 'avatar', 'cover'];
        $shop = RestaurantService::getInstance()->getShopByUserId($this->userId(), $columns);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前店铺不存在');
        }
        return $this->success($shop);
    }

    public function hotelListTotals()
    {
        return $this->success([
            ProviderRestaurantService::getInstance()->getListTotal($this->userId(), 1),
            ProviderRestaurantService::getInstance()->getListTotal($this->userId(), 0),
            ProviderRestaurantService::getInstance()->getListTotal($this->userId(), 2),
        ]);
    }

    public function providerRestaurantList()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();

        $page = ProviderRestaurantService::getInstance()->getUserRestaurantList($this->userId(), $input, ['id', 'hotel_id', 'status', 'failure_reason', 'created_at', 'updated_at']);
        $providerRestaurantList = collect($page->items());
        $hotelIds = $providerRestaurantList->pluck('hotel_id')->toArray();
        $hotelList = RestaurantService::getInstance()->getRestaurantListByIds($hotelIds, ['id', 'name', 'cover', 'grade', 'address'])->keyBy('id');
        $list = $providerRestaurantList->map(function (ProviderRestaurant $providerRestaurant) use ($hotelList) {
            /** @var Restaurant $hotel */
            $hotel = $hotelList->get($providerRestaurant->hotel_id);
            $providerRestaurant['hotel_cover'] = $hotel->cover;
            $providerRestaurant['hotel_name'] = $hotel->name;
            $providerRestaurant['hotel_grade'] = $hotel->grade;
            $providerRestaurant['hotel_address'] = $hotel->address;
            return $providerRestaurant;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function applyRestaurant()
    {
        $hotelId = $this->verifyRequiredId('hotelId');

        if (is_null($this->user()->hotelShop)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '暂无权限申请添加酒店');
        }

        $hotel = RestaurantService::getInstance()->getRestaurantById($hotelId);
        if (is_null($hotel)) {
            return $this->fail(CodeResponse::NOT_FOUND, '酒店不存在');
        }

        $providerRestaurant = ProviderRestaurantService::getInstance()->getRestaurantByRestaurantId($this->userId(), $hotelId);
        if (!is_null($providerRestaurant)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '您已添加过当前酒店');
        }

        $providerRestaurant = ProviderRestaurant::new();
        $providerRestaurant->user_id = $this->userId();
        $providerRestaurant->provider_id = $this->user()->hotelProvider->id;
        $providerRestaurant->hotel_id = $hotelId;
        $providerRestaurant->save();
        return $this->success();
    }

    public function deleteProviderRestaurant()
    {
        $id = $this->verifyRequiredId('id');
        $hotel = ProviderRestaurantService::getInstance()->getUserRestaurantById($this->userId(), $id);
        if (is_null($hotel)) {
            return $this->fail(CodeResponse::NOT_FOUND, '供应商酒店不存在');
        }
        $hotel->delete();
        return $this->success();
    }

    public function providerRestaurantOptions()
    {
        $hotelIds = ProviderRestaurantService::getInstance()->getUserRestaurantOptions($this->userId())->pluck('hotel_id')->toArray();
        $hotelOptions = RestaurantService::getInstance()->getRestaurantListByIds($hotelIds, ['id', 'name']);
        return $this->success($hotelOptions);
    }
}
