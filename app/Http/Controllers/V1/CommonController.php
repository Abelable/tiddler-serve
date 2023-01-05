<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\MerchantOrderService;
use App\Services\MerchantService;
use App\Services\ShopService;
use App\Utils\AliOssServe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yansongda\LaravelPay\Facades\Pay;

class CommonController extends Controller
{
    protected $only = ['getOssConfig'];

    public function ossConfig()
    {
        $config = AliOssServe::new()->getOssConfig();
        return $this->success($config);
    }

    public function wxPayNotify()
    {
        $data = Pay::wechat()->verify()->toArray();

        if (strpos($data['body'], 'merchant_order_sn')) {
            Log::info('merchant_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                $order = MerchantOrderService::getInstance()->handleWxPayNotify($data);
                $merchant = MerchantService::getInstance()->paySuccess($order->merchant_id);
                ShopService::getInstance()->createShop($this->userId(), $merchant->id, $merchant->type, $merchant->shop_name, $merchant->shop_category_id);
            });
        }

        return Pay::wechat()->success();
    }
}
