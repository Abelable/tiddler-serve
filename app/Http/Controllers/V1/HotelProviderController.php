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
            'orderId' => $providerOrder->id
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

    public function scenicListTotals()
    {
        return $this->success([
            ProviderHotelService::getInstance()->getListTotal($this->userId(), 1),
            ProviderHotelService::getInstance()->getListTotal($this->userId(), 0),
            ProviderHotelService::getInstance()->getListTotal($this->userId(), 2),
        ]);
    }

    public function providerHotelList()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();

        $page = ProviderHotelService::getInstance()->getUserSpotList($this->userId(), $input, ['id', 'scenic_id', 'status', 'failure_reason', 'created_at', 'updated_at']);
        $providerHotelList = collect($page->items());
        $scenicIds = $providerHotelList->pluck('scenic_id')->toArray();
        $scenicList = HotelService::getInstance()->getHotelListByIds($scenicIds, ['id', 'name', 'image_list', 'level', 'address'])->keyBy('id');
        $list = $providerHotelList->map(function (ProviderHotel $providerHotel) use ($scenicList) {
            /** @var Hotel $scenic */
            $scenic = $scenicList->get($providerHotel->scenic_id);
            $providerHotel['scenic_image'] = json_decode($scenic->image_list)[0];
            $providerHotel['scenic_name'] = $scenic->name;
            $providerHotel['scenic_level'] = $scenic->level;
            $providerHotel['scenic_address'] = $scenic->address;
            return $providerHotel;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function applyHotel()
    {
        $scenicId = $this->verifyRequiredId('scenicId');

        if (is_null($this->user()->scenicShop)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '暂无权限申请添加景点');
        }

        $scenicSpot = HotelService::getInstance()->getHotelById($scenicId);
        if (is_null($scenicSpot)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景点不存在');
        }

        $providerHotel = ProviderHotelService::getInstance()->getSpotByHotelId($this->userId(), $scenicId);
        if (!is_null($providerHotel)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '您已添加过当前景点');
        }

        $providerHotel = ProviderHotel::new();
        $providerHotel->user_id = $this->userId();
        $providerHotel->provider_id = $this->user()->scenicProvider->id;
        $providerHotel->scenic_id = $scenicId;
        $providerHotel->save();
        return $this->success();
    }

    public function deleteProviderHotel()
    {
        $id = $this->verifyRequiredId('id');
        $spot = ProviderHotelService::getInstance()->getUserSpotById($this->userId(), $id);
        if (is_null($spot)) {
            return $this->fail(CodeResponse::NOT_FOUND, '供应商景点不存在');
        }
        $spot->delete();
        return $this->success();
    }

    public function providerHotelOptions()
    {
        $scenicIds = ProviderHotelService::getInstance()->getUserHotelOptions($this->userId())->pluck('scenic_id')->toArray();
        $scenicOptions = HotelService::getInstance()->getHotelListByIds($scenicIds, ['id', 'name']);
        return $this->success($scenicOptions);
    }
}
