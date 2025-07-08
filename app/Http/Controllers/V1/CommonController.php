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
use App\Services\MerchantService;
use App\Services\ScenicShopDepositPaymentLogService;
use App\Services\ScenicShopDepositService;
use App\Services\ShopDepositPaymentLogService;
use App\Services\OrderService;
use App\Services\RestaurantService;
use App\Services\ScenicOrderService;
use App\Services\ScenicProviderOrderService;
use App\Services\ScenicMerchantService;
use App\Services\ScenicShopService;
use App\Services\SetMealOrderService;
use App\Services\ShopDepositService;
use App\Services\ShopService;
use App\Utils\AliOssServe;
use App\Utils\WxMpServe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yansongda\LaravelPay\Facades\Pay;

class CommonController extends Controller
{
    protected $except = ['ossConfig', 'wxPayNotify'];

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
        $qrcode = 'data:image/png;base64,' . base64_encode($imageData);

        return $this->success($qrcode);

//        return response($imageData)
//            ->header('Content-Type', 'image/png')
//            ->header('Content-Disposition', 'inline');
    }

    public function qrCode()
    {
        $code = $this->verifyRequiredId('code');
        $qrCode = QrCode::format('png')->size(300)->generate($code);
        return response($qrCode)->header('Content-Type', 'image/png');
    }

    private function fileToBase64($file){
        $base64_file = '';
        if(file_exists($file)){
            $mime_type= mime_content_type($file);
            $base64_data = base64_encode(file_get_contents($file));
            $base64_file = 'data:'.$mime_type.';base64,'.$base64_data;
        }
        return $base64_file;
    }

    public function wxPayNotify()
    {
        $data = Pay::wechat()->verify()->toArray();

        if (strpos($data['attach'], 'scenic_shop_id') !== false) {
            Log::info('scenic_shop_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                $log = ScenicShopDepositPaymentLogService::getInstance()->wxPaySuccess($data);
                ScenicMerchantService::getInstance()->paySuccess($log->merchant_id);
                ScenicShopService::getInstance()->paySuccess($log->shop_id);
                ScenicShopDepositService::getInstance()->updateDeposit($log->shop_id, 1, $log->payment_amount);
            });
        }

        if (strpos($data['attach'], 'scenic_order_sn') !== false) {
            Log::info('scenic_order_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                ScenicOrderService::getInstance()->wxPaySuccess($data);
            });
        }

        if (strpos($data['attach'], 'shop_id') !== false) {
            Log::info('shop_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                $log = ShopDepositPaymentLogService::getInstance()->wxPaySuccess($data);
                MerchantService::getInstance()->paySuccess($log->merchant_id);
                ShopService::getInstance()->paySuccess($log->shop_id);
                ShopDepositService::getInstance()->updateDeposit($log->shop_id, 1, $log->payment_amount);
            });
        }

        if (strpos($data['attach'], 'order_sn_list') !== false) {
            Log::info('order_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                OrderService::getInstance()->wxPaySuccess($data);
            });
        }

        if (strpos($data['attach'], 'hotel_provider_order_sn') !== false) {
            Log::info('hotel_provider_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                $order = HotelProviderOrderService::getInstance()->wxPaySuccess($data);
                HotelProviderService::getInstance()->paySuccess($order->provider_id);
                HotelShopService::getInstance()->paySuccess($order->provider_id);
            });
        }

        if (strpos($data['attach'], 'hotel_order_sn') !== false) {
            Log::info('hotel_order_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                HotelOrderService::getInstance()->wxPaySuccess($data);
            });
        }

        if (strpos($data['attach'], 'catering_provider_order_sn') !== false) {
            Log::info('catering_provider_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                $order = CateringProviderOrderService::getInstance()->wxPaySuccess($data);
                CateringProviderService::getInstance()->paySuccess($order->provider_id);
                RestaurantService::getInstance()->paySuccess($order->provider_id);
            });
        }

        if (strpos($data['attach'], 'meal_ticket_order_sn') !== false) {
            Log::info('meal_ticket_order_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                MealTicketOrderService::getInstance()->wxPaySuccess($data);
            });
        }

        if (strpos($data['attach'], 'set_meal_order_sn') !== false) {
            Log::info('set_meal_order_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                SetMealOrderService::getInstance()->wxPaySuccess($data);
            });
        }

        return Pay::wechat()->success();
    }
}
