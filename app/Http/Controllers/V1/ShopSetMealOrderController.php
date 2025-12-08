<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Mall\Catering\OrderSetMeal;
use App\Models\Mall\Catering\SetMealOrder;
use App\Services\Mall\Catering\CateringShopManagerService;
use App\Services\Mall\Catering\OrderSetMealService;
use App\Services\Mall\Catering\RestaurantManagerService;
use App\Services\Mall\Catering\SetMealOrderService;
use App\Services\Mall\Catering\SetMealVerifyService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Enums\SetMealOrderStatus;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

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
        $list = $this->handleOrderList(collect($page->items()));

        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        $shopId = $this->verifyId('shopId');
        $keywords = $this->verifyRequiredString('keywords');

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
        $orderList = SetMealOrderService::getInstance()->searchShopOrderList($shopId, $statusList, $keywords);
        $list = $this->handleOrderList($orderList);

        return $this->success($list);
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

    private function handleOrderList($orderList)
    {
        $userIds = $orderList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()
            ->getListByIds($userIds, ['id', 'avatar', 'nickname'])
            ->keyBy('id');

        $orderIds = $orderList->pluck('id')->toArray();
        $setMealList = OrderSetMealService::getInstance()->getListByOrderIds($orderIds)->keyBy('order_id');

        return $orderList->map(function (SetMealOrder $order) use ($userList, $setMealList) {
            $userInfo = $userList->get($order->user_id);

            /** @var OrderSetMeal $setMeal */
            $setMeal = $setMealList->get($order->id);
            $setMeal->package_details = json_decode($setMeal->package_details);
            $setMeal->use_time_list = json_decode($setMeal->use_time_list) ?: [];
            $setMeal->use_rules = json_decode($setMeal->use_rules) ?: [];

            return [
                'id' => $order->id,
                'status' => $order->status,
                'statusDesc' => SetMealOrderStatus::TEXT_MAP[$order->status],
                'userInfo' => $userInfo,
                'setMealInfo' => $setMeal,
                'totalPrice' => $order->total_price,
                'deduction_balance' => $order->deduction_balance,
                'paymentAmount' => $order->payment_amount,
                'consignee' => $order->consignee,
                'mobile' => $order->mobile,
                'orderSn' => $order->order_sn
            ];
        });
    }

    public function detail()
    {
        $shopId = $this->verifyRequiredInteger('shopId');
        $orderId = $this->verifyRequiredId('orderId');
        $columns = [
            'id',
            'user_id',
            'order_sn',
            'status',
            'consignee',
            'mobile',
            'total_price',
            'deduction_balance',
            'payment_amount',
            'pay_time',
            'approve_time',
            'confirm_time',
            'created_at',
            'updated_at',
        ];

        $order = SetMealOrderService::getInstance()->getShopOrder($shopId, $orderId, $columns);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }

        $userInfo = UserService::getInstance()->getUserById($order->user_id);
        $order['userInfo'] = $userInfo;
        unset($order->user_id);

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

        $order = SetMealOrderService::getInstance()->getApprovedOrderById($verifyCodeInfo->order_id);
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
