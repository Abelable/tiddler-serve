<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\CateringProviderOrderService;
use App\Services\CateringProviderService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CateringProviderInput;
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

        CateringProviderService::getInstance()->createProvider($input, $this->userId());

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
            return $this->fail(CodeResponse::NOT_FOUND, '餐饮商家信息不存在');
        }
        $provider->delete();
        return $this->success();
    }
}
