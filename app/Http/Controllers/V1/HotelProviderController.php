<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\HotelProviderOrderService;
use App\Services\HotelProviderService;
use App\Services\HotelShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\HotelProviderInput;
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
}
