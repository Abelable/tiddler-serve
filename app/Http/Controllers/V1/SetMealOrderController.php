<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\SetMealOrder;
use App\Models\OrderSetMeal;
use App\Services\SetMealOrderService;
use App\Services\OrderSetMealService;
use App\Utils\CodeResponse;
use App\Utils\Enums\SetMealOrderEnums;
use App\Utils\Inputs\SetMealOrderInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yansongda\LaravelPay\Facades\Pay;

class SetMealOrderController extends Controller
{
    public function paymentAmount()
    {
        $setMealId = $this->verifyRequiredId('setMealId');
        $num = $this->verifyRequiredInteger('num');

        list($paymentAmount) = SetMealOrderService::getInstance()->calcPaymentAmount($setMealId, $num);

        return $this->success($paymentAmount);
    }

    public function submit()
    {
        /** @var SetMealOrderInput $input */
        $input = SetMealOrderInput::new();

        // 分布式锁，防止重复请求
        $lockKey = sprintf('create_set_meal_order_%s_%s', $this->userId(), md5(serialize($input)));
        $lock = Cache::lock($lockKey, 5);
        if (!$lock->get()) {
            $this->fail(CodeResponse::FAIL, '请勿重复提交订单');
        }

        $orderId = DB::transaction(function () use ($input) {
            return SetMealOrderService::getInstance()->createOrder($this->user(), $input);
        });

        return $this->success($orderId);
    }

    public function payParams()
    {
        $orderId = $this->verifyRequiredInteger('orderId');
        $order = SetMealOrderService::getInstance()->createWxPayOrder($this->userId(), $orderId, $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($order);
        return $this->success($payParams);
    }

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');

        $statusList = $this->statusList($status);
        $page = SetMealOrderService::getInstance()->getOrderListByStatus($this->userId(), $statusList, $input);
        $list = $this->orderList($page);

        return $this->success($this->paginate($page, $list));
    }

    public function providerList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');

        $statusList = $this->statusList($status);
        $page = SetMealOrderService::getInstance()->getProviderOrderList($this->user()->cateringProvider->id, $statusList, $input);
        $list = $this->orderList($page);

        return $this->success($this->paginate($page, $list));
    }

    private function statusList($status) {
        switch ($status) {
            case 1:
                $statusList = [SetMealOrderEnums::STATUS_CREATE];
                break;
            case 2:
                $statusList = [SetMealOrderEnums::STATUS_PAY];
                break;
            case 3:
                $statusList = [SetMealOrderEnums::STATUS_CONFIRM, SetMealOrderEnums::STATUS_AUTO_CONFIRM];
                break;
            case 4:
                $statusList = [SetMealOrderEnums::STATUS_REFUND, SetMealOrderEnums::STATUS_REFUND_CONFIRM];
                break;
            default:
                $statusList = [];
                break;
        }

        return $statusList;
    }

    private function orderList($page)
    {
        $orderList = collect($page->items());
        $orderIds = $orderList->pluck('id')->toArray();
        $setMealList = OrderSetMealService::getInstance()->getListByOrderIds($orderIds)->keyBy('order_id');
        return $orderList->map(function (SetMealOrder $order) use ($setMealList) {
            /** @var OrderSetMeal $setMeal */
            $setMeal = $setMealList->get($order->id);
            $setMeal->package_details = json_decode($setMeal->package_details);
            $setMeal->use_time_list = json_decode($setMeal->use_time_list) ?: [];
            $setMeal->use_rules = json_decode($setMeal->use_rules) ?: [];

            return [
                'id' => $order->id,
                'status' => $order->status,
                'statusDesc' => SetMealOrderEnums::STATUS_TEXT_MAP[$order->status],
                'restaurantId' => $order->restaurant_id,
                'restaurantName' => $order->restaurant_name,
                'setMealInfo' => $setMeal,
                'paymentAmount' => $order->payment_amount,
                'consignee' => $order->consignee,
                'mobile' => $order->mobile,
                'orderSn' => $order->order_sn
            ];
        });
    }

    public function cancel()
    {
        $id = $this->verifyRequiredId('id');
        SetMealOrderService::getInstance()->userCancel($this->userId(), $id);
        return $this->success();
    }

    public function confirm()
    {
        $id = $this->verifyRequiredId('id');
        SetMealOrderService::getInstance()->confirm($this->userId(), $id);
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        DB::transaction(function () use ($id) {
            SetMealOrderService::getInstance()->delete($this->userId(), $id);
        });
        return $this->success();
    }

    public function refund()
    {
        $id = $this->verifyRequiredId('id');
        SetMealOrderService::getInstance()->refund($this->userId(), $id);
        return $this->success();
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $columns = [
            'id',
            'order_sn',
            'status',
            'consignee',
            'mobile',
            'restaurant_id',
            'restaurant_name',
            'payment_amount',
            'pay_time',
            'confirm_time',
            'created_at',
            'updated_at',
        ];
        $order = SetMealOrderService::getInstance()->getOrderById($this->userId(), $id, $columns);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }

        $setMeal = OrderSetMealService::getInstance()->getSetMealByOrderId($order->id);
        unset($setMeal->id);
        $setMeal->package_details = json_decode($setMeal->package_details);
        $setMeal->use_time_list = json_decode($setMeal->use_time_list) ?: [];
        $setMeal->use_rules = json_decode($setMeal->use_rules) ?: [];
        $order['setMealInfo'] = $setMeal;

        return $this->success($order);
    }
}
