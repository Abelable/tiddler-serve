<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\CateringProviderOrderService;
use App\Services\CateringProviderService;
use App\Services\HotelOrderService;
use App\Services\HotelProviderOrderService;
use App\Services\HotelProviderService;
use App\Services\HotelShopService;
use App\Services\MealTicketOrderService;
use App\Services\MerchantOrderService;
use App\Services\MerchantService;
use App\Services\OrderService;
use App\Services\RestaurantService;
use App\Services\ScenicOrderService;
use App\Services\ScenicProviderOrderService;
use App\Services\ScenicProviderService;
use App\Services\ScenicShopService;
use App\Services\SetMealOrderService;
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
                MerchantService::getInstance()->paySuccess($order->merchant_id);
                ShopService::getInstance()->paySuccess($order->merchant_id);
            });
        }

        if (strpos($data['body'], 'order_sn_list')) {
            Log::info('order_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                OrderService::getInstance()->wxPaySuccess($data);
            });
        }

        if (strpos($data['body'], 'scenic_provider_order_sn')) {
            Log::info('scenic_provider_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                $order = ScenicProviderOrderService::getInstance()->wxPaySuccess($data);
                ScenicProviderService::getInstance()->paySuccess($order->provider_id);
                ScenicShopService::getInstance()->paySuccess($order->provider_id);
            });
        }

        if (strpos($data['body'], 'scenic_order_sn')) {
            Log::info('scenic_order_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                ScenicOrderService::getInstance()->wxPaySuccess($data);
            });
        }

        if (strpos($data['body'], 'hotel_provider_order_sn')) {
            Log::info('hotel_provider_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                $order = HotelProviderOrderService::getInstance()->wxPaySuccess($data);
                HotelProviderService::getInstance()->paySuccess($order->provider_id);
                HotelShopService::getInstance()->paySuccess($order->provider_id);
            });
        }

        if (strpos($data['body'], 'hotel_order_sn')) {
            Log::info('hotel_order_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                HotelOrderService::getInstance()->wxPaySuccess($data);
            });
        }

        if (strpos($data['body'], 'catering_provider_order_sn')) {
            Log::info('catering_provider_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                $order = CateringProviderOrderService::getInstance()->wxPaySuccess($data);
                CateringProviderService::getInstance()->paySuccess($order->provider_id);
                RestaurantService::getInstance()->paySuccess($order->provider_id);
            });
        }

        if (strpos($data['body'], 'meal_ticket_order_sn')) {
            Log::info('meal_ticket_order_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                MealTicketOrderService::getInstance()->wxPaySuccess($data);
            });
        }

        if (strpos($data['body'], 'set_meal_order_sn')) {
            Log::info('set_meal_order_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                SetMealOrderService::getInstance()->wxPaySuccess($data);
            });
        }

        return Pay::wechat()->success();
    }
}
