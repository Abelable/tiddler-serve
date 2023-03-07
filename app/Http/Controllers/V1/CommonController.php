<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\MerchantOrderService;
use App\Services\MerchantService;
use App\Services\OrderService;
use App\Services\ShopService;
use App\Utils\AliOssServe;
use App\Utils\WxMpServe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yansongda\LaravelPay\Facades\Pay;

class CommonController extends Controller
{
    protected $except = ['wxPayNotify'];

    public function ossConfig()
    {
        $config = AliOssServe::new()->getOssConfig();
        return $this->success($config);
    }

    public function wxQRCode()
    {
        $scene = $this->verifyRequiredString('scene');
        $page = $this->verifyRequiredString('page');

        $imageData = WxMpServe::new()->getQRCode($scene, $page);

        return response($imageData)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'inline');
    }

    public function wxPayNotify()
    {
        $data = Pay::wechat()->verify()->toArray();

        if (strpos($data['body'], 'merchant_order_sn')) {
            Log::info('merchant_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                $order = MerchantOrderService::getInstance()->wxPaySuccess($data);
                $merchant = MerchantService::getInstance()->paySuccess($order->merchant_id);
                $shop = ShopService::getInstance()->createShop($this->userId(), $merchant->id, $merchant->type, $merchant->shop_name, $merchant->shop_category_id);
                $user = $this->user();
                $user->shop_id = $shop->id;
                $user->save();
            });
        }

        if (strpos($data['body'], 'order_sn_list')) {
            Log::info('order_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                OrderService::getInstance()->wxPaySuccess($data);
            });
        }

        return Pay::wechat()->success();
    }
}
