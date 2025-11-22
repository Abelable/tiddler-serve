<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ComplaintOptionService;
use App\Services\EvaluationTagService;
use App\Services\HotelMerchantService;
use App\Services\HotelOrderService;
use App\Services\HotelShopDepositPaymentLogService;
use App\Services\HotelShopDepositService;
use App\Services\HotelShopService;
use App\Services\Mall\Catering\CateringMerchantService;
use App\Services\Mall\Catering\CateringShopDepositPaymentLogService;
use App\Services\Mall\Catering\CateringShopDepositService;
use App\Services\Mall\Catering\CateringShopService;
use App\Services\MealTicketOrderService;
use App\Services\MerchantService;
use App\Services\OrderService;
use App\Services\ScenicMerchantService;
use App\Services\ScenicOrderService;
use App\Services\ScenicOrderTicketService;
use App\Services\ScenicShopDepositPaymentLogService;
use App\Services\ScenicShopDepositService;
use App\Services\ScenicShopService;
use App\Services\ScenicTicketService;
use App\Services\SetMealOrderService;
use App\Services\ShopDepositPaymentLogService;
use App\Services\ShopDepositService;
use App\Services\ShopService;
use App\Services\TaskService;
use App\Services\UserTaskService;
use App\Utils\AliOssServe;
use App\Utils\Enums\ProductType;
use App\Utils\ExpressServe;
use App\Utils\WxMpServe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yansongda\LaravelPay\Facades\Pay;

class CommonController extends Controller
{
    protected $except = ['ossConfig', 'wxPayNotify', 'qrCode'];

    public function ossConfig()
    {
        $config = AliOssServe::new()->getOssConfig();
        return $this->success($config);
    }

    public function wxQrCode()
    {
        $scene = $this->verifyRequiredString('scene');
        $page = $this->verifyRequiredString('page');

        $imageData = WxMpServe::new()->getQrCode($scene, $page);
        $qrCode = 'data:image/png;base64,' . base64_encode($imageData);

        return $this->success($qrCode);

//        return response($imageData)
//            ->header('Content-Type', 'image/png')
//            ->header('Content-Disposition', 'inline');
    }

    public function qrCode()
    {
        $code = $this->verifyRequiredId('code');
        $qrCode = QrCode::format('png')->size(400)->generate($code);
        return response($qrCode)->header('Content-Type', 'image/png');
    }

    public function shippingInfo()
    {
        $shipCode = $this->verifyRequiredString('shipCode');
        $shipSn = $this->verifyRequiredString('shipSn');
        $mobile = $this->verifyString('mobile');

        $traces = ExpressServe::new()->track($shipCode, $shipSn, $mobile);
        return $this->success($traces);
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

                // 邀请商家入驻活动
                $userTask = UserTaskService::getInstance()->getByMerchantId(1, $log->merchant_id, 1);
                if (!is_null($userTask)) {
                    $userTask->step = 2;
                    $userTask->save();
                }
            });
        }

        if (strpos($data['attach'], 'scenic_order_sn') !== false) {
            Log::info('scenic_order_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                $order = ScenicOrderService::getInstance()->wxPaySuccess($data);

                // 邀请商家入驻活动
                $userTask = UserTaskService::getInstance()
                    ->getByMerchantId(1, $order->shopInfo->merchant_id, 3);
                if (!is_null($userTask)) {
                    $scenicOrderTicket = ScenicOrderTicketService::getInstance()->getTicketByOrderId($order->id);
                    $scenicTicket = ScenicTicketService::getInstance()->getTicketById($scenicOrderTicket->ticket_id);
                    if (in_array($userTask->product_id, $scenicTicket->scenicIds())) {
                        $userTask->status = 2;
                        $userTask->step = 4;
                        $userTask->order_id = $order->id;
                        $userTask->finish_time = now()->format('Y-m-d\TH:i:s');
                        $userTask->save();

                        $task = TaskService::getInstance()->getTaskById($userTask->task_id);
                        $task->status = 3;
                        $task->save();
                    }
                }
            });
        }

        if (strpos($data['attach'], 'hotel_shop_id') !== false) {
            Log::info('hotel_shop_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                $log = HotelShopDepositPaymentLogService::getInstance()->wxPaySuccess($data);
                HotelMerchantService::getInstance()->paySuccess($log->merchant_id);
                HotelShopService::getInstance()->paySuccess($log->shop_id);
                HotelShopDepositService::getInstance()->updateDeposit($log->shop_id, 1, $log->payment_amount);

                // 邀请商家入驻活动
                $userTask = UserTaskService::getInstance()->getByMerchantId(2, $log->merchant_id, 1);
                if (!is_null($userTask)) {
                    $userTask->step = 2;
                    $userTask->save();
                }
            });
        }

        if (strpos($data['attach'], 'hotel_order_sn') !== false) {
            Log::info('hotel_order_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                $order = HotelOrderService::getInstance()->wxPaySuccess($data);

                // 邀请商家入驻活动
                $userTask = UserTaskService::getInstance()
                    ->getByMerchantId(2, $order->shopInfo->merchant_id, 3);
                if (!is_null($userTask) && $userTask->product_id == $order->hotel_id) {
                    $userTask->status = 2;
                    $userTask->step = 4;
                    $userTask->order_id = $order->id;
                    $userTask->finish_time = now()->format('Y-m-d\TH:i:s');
                    $userTask->save();

                    $task = TaskService::getInstance()->getTaskById($userTask->task_id);
                    $task->status = 3;
                    $task->save();
                }
            });
        }

        if (strpos($data['attach'], 'catering_shop_id') !== false) {
            Log::info('catering_shop_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                $log = CateringShopDepositPaymentLogService::getInstance()->wxPaySuccess($data);
                CateringMerchantService::getInstance()->paySuccess($log->merchant_id);
                CateringShopService::getInstance()->paySuccess($log->shop_id);
                CateringShopDepositService::getInstance()->updateDeposit($log->shop_id, 1, $log->payment_amount);

                // 邀请商家入驻活动
                $userTask = UserTaskService::getInstance()->getByMerchantId(3, $log->merchant_id, 1);
                if (!is_null($userTask)) {
                    $userTask->step = 2;
                    $userTask->save();
                }
            });
        }

        if (strpos($data['attach'], 'meal_ticket_order_sn') !== false) {
            Log::info('meal_ticket_order_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                $order = MealTicketOrderService::getInstance()->wxPaySuccess($data);

                // 邀请商家入驻活动
                $userTask = UserTaskService::getInstance()
                    ->getByMerchantId(3, $order->shopInfo->merchant_id, 3);
                if (!is_null($userTask) && $userTask->product_id == $order->restaurant_id) {
                    $userTask->status = 2;
                    $userTask->step = 4;
                    $userTask->order_id = $order->id;
                    $userTask->product_type = ProductType::MEAL_TICKET;
                    $userTask->finish_time = now()->format('Y-m-d\TH:i:s');
                    $userTask->save();

                    $task = TaskService::getInstance()->getTaskById($userTask->task_id);
                    $task->status = 3;
                    $task->save();
                }
            });
        }

        if (strpos($data['attach'], 'set_meal_order_sn') !== false) {
            Log::info('set_meal_order_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                $order = SetMealOrderService::getInstance()->wxPaySuccess($data);

                // 邀请商家入驻活动
                $userTask = UserTaskService::getInstance()
                    ->getByMerchantId(3, $order->shopInfo->merchant_id, 3);
                if (!is_null($userTask) && $userTask->product_id == $order->restaurant_id) {
                    $userTask->status = 2;
                    $userTask->step = 4;
                    $userTask->order_id = $order->id;
                    $userTask->product_type = ProductType::SET_MEAL;
                    $userTask->finish_time = now()->format('Y-m-d\TH:i:s');
                    $userTask->save();

                    $task = TaskService::getInstance()->getTaskById($userTask->task_id);
                    $task->status = 3;
                    $task->save();
                }
            });
        }

        if (strpos($data['attach'], 'shop_id') !== false) {
            Log::info('shop_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                $log = ShopDepositPaymentLogService::getInstance()->wxPaySuccess($data);
                MerchantService::getInstance()->paySuccess($log->merchant_id);
                ShopService::getInstance()->paySuccess($log->shop_id);
                ShopDepositService::getInstance()->updateDeposit($log->shop_id, 1, $log->payment_amount);

                // 邀请商家入驻活动
                $userTask = UserTaskService::getInstance()->getByMerchantId(4, $log->merchant_id, 1);
                if (!is_null($userTask)) {
                    $userTask->step = 2;
                    $userTask->save();
                }
            });
        }

        if (strpos($data['attach'], 'order_sn_list') !== false) {
            Log::info('order_wx_pay_notify', $data);
            DB::transaction(function () use ($data) {
                $orderList = OrderService::getInstance()->wxPaySuccess($data);

                /** @var Order $order */
                foreach ($orderList as $order) {
                    // 邀请商家入驻活动
                    $userTask = UserTaskService::getInstance()
                        ->getByMerchantId(4, $order->shopInfo->merchant_id, 3);
                    if (!is_null($userTask)) {
                        $userTask->status = 2;
                        $userTask->step = 4;
                        $userTask->order_id = $order->id;
                        $userTask->finish_time = now()->format('Y-m-d\TH:i:s');
                        $userTask->save();

                        $task = TaskService::getInstance()->getTaskById($userTask->task_id);
                        $task->status = 3;
                        $task->save();
                    }
                }
            });
        }

        return Pay::wechat()->success();
    }

    public function evaluationTagList()
    {
        $scene = $this->verifyRequiredInteger('scene');
        $type = $this->verifyRequiredInteger('type');

        $tagList = EvaluationTagService::getInstance()->getEvaluationTagOptions($scene, $type, ['id', 'content']);

        return $this->success($tagList);
    }

    public function complaintOptions()
    {
        $type = $this->verifyRequiredInteger('type');
        $tagList = ComplaintOptionService::getInstance()->getComplaintOptions($type, ['id', 'title', 'content']);
        return $this->success($tagList);
    }
}
