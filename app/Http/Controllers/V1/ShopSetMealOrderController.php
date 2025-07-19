<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Catering\OrderSetMeal;
use App\Models\Catering\SetMealOrder;
use App\Services\AccountService;
use App\Services\CommissionService;
use App\Services\Mall\Catering\CateringShopManagerService;
use App\Services\Mall\Catering\RestaurantManagerService;
use App\Services\OrderSetMealService;
use App\Services\PromoterService;
use App\Services\RelationService;
use App\Services\RestaurantService;
use App\Services\SetMealOrderService;
use App\Services\SetMealService;
use App\Services\SetMealVerifyService;
use App\Utils\CodeResponse;
use App\Utils\Enums\SetMealOrderStatus;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\SetMealOrderInput;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yansongda\LaravelPay\Facades\Pay;

class ShopSetMealOrderController extends Controller
{
    public function total()
    {
        $shopId = $this->verifyRequiredId('shopId');

        return $this->success([
            SetMealOrderService::getInstance()->getShopTotal($shopId, $this->statusList(1)),
            SetMealOrderService::getInstance()->getShopTotal($shopId, $this->statusList(2)),
            0,
            SetMealOrderService::getInstance()->getShopTotal($shopId, [SetMealOrderStatus::REFUNDING]),
        ]);
    }

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $shopId = $this->verifyId('shopId');
        $status = $this->verifyRequiredInteger('status');

        $statusList = $this->statusList($status);
        $page = SetMealOrderService::getInstance()->getShopOrderList($shopId, $statusList, $input);
        $list = $this->handleOrderList($page);

        return $this->success($this->paginate($page, $list));
    }

    private function statusList($status) {
        switch ($status) {
            case 1:
                $statusList = [SetMealOrderStatus::PAID];
                break;
            case 2:
                $statusList = [SetMealOrderStatus::MERCHANT_APPROVED];
                break;
            case 3:
                $statusList = [SetMealOrderStatus::FINISHED];
                break;
            case 4:
                $statusList = [
                    SetMealOrderStatus::REFUNDING,
                    SetMealOrderStatus::REFUNDED
                ];
                break;
            default:
                $statusList = [
                    SetMealOrderStatus::PAID,
                    SetMealOrderStatus::REFUNDING,
                    SetMealOrderStatus::REFUNDED,
                    SetMealOrderStatus::MERCHANT_REJECTED,
                    SetMealOrderStatus::MERCHANT_APPROVED,
                    SetMealOrderStatus::CONFIRMED,
                    SetMealOrderStatus::AUTO_CONFIRMED,
                    SetMealOrderStatus::ADMIN_CONFIRMED,
                    SetMealOrderStatus::FINISHED,
                    SetMealOrderStatus::AUTO_FINISHED,
                ];
                break;
        }

        return $statusList;
    }

    private function handleOrderList($page)
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
                'statusDesc' => SetMealOrderStatus::TEXT_MAP[$order->status],
                'setMealInfo' => $setMeal,
                'paymentAmount' => $order->payment_amount,
                'consignee' => $order->consignee,
                'mobile' => $order->mobile,
                'orderSn' => $order->order_sn
            ];
        });
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
            'payment_amount',
            'pay_time',
            'approve_time',
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

    public function approve()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $orderId = $this->verifyRequiredId('orderId');
        SetMealOrderService::getInstance()->approve($shopId, $orderId);
        return $this->success();
    }

    public function refund()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $orderId = $this->verifyRequiredId('orderId');
        SetMealOrderService::getInstance()->shopRefund($shopId, $orderId);
        return $this->success();
    }

    public function verify()
    {
        $code = $this->verifyRequiredString('code');

        $verifyCodeInfo = SetMealVerifyService::getInstance()->getByCode($code);
        if (is_null($verifyCodeInfo)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '无效核销码');
        }

        $order = SetMealOrderService::getInstance()->getPaidOrderById($verifyCodeInfo->order_id);
        if (is_null($order)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '订单不存在');
        }

        $managerIds = RestaurantManagerService::getInstance()
            ->getListByRestaurantId($verifyCodeInfo->restaurant_id)
            ->pluck('manager_id')
            ->toArray();
        $managerUserIds = array_unique(CateringShopManagerService::getInstance()
            ->getListByIds($managerIds)->pluck('user_id')->toArray());
        if ($order->shop_id != $this->user()->cateringShop->id && !in_array($this->userId(), $managerUserIds)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '非当前餐馆核销员，无法核销');
        }

        DB::transaction(function () use ($verifyCodeInfo, $order) {
            SetMealVerifyService::getInstance()->verify($verifyCodeInfo, $this->userId());
            SetMealOrderService::getInstance()->userConfirm($order->user_id, $order->id);
        });

        return $this->success();
    }
}
