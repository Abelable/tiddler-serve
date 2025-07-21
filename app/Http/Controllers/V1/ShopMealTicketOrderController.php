<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Catering\MealTicketOrder;
use App\Models\Catering\OrderMealTicket;
use App\Services\Mall\Catering\CateringShopManagerService;
use App\Services\Mall\Catering\RestaurantManagerService;
use App\Services\MealTicketOrderService;
use App\Services\MealTicketVerifyService;
use App\Services\OrderMealTicketService;
use App\Utils\CodeResponse;
use App\Utils\Enums\MealTicketOrderStatus;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

class ShopMealTicketOrderController extends Controller
{
    public function total()
    {
        $shopId = $this->verifyRequiredId('shopId');

        return $this->success([
            MealTicketOrderService::getInstance()->getShopTotal($shopId, $this->statusList(1)),
            MealTicketOrderService::getInstance()->getShopTotal($shopId, $this->statusList(2)),
            0,
            MealTicketOrderService::getInstance()->getShopTotal($shopId, [MealTicketOrderStatus::REFUNDING]),
        ]);
    }

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $shopId = $this->verifyId('shopId');
        $status = $this->verifyRequiredInteger('status');

        $statusList = $this->statusList($status);
        $page = MealTicketOrderService::getInstance()->getShopOrderList($shopId, $statusList, $input);
        $orderList = collect($page->items());
        $list = $this->handleOrderList($orderList);

        return $this->success($this->paginate($page, $list));
    }

    private function statusList($status) {
        switch ($status) {
            case 1:
                $statusList = [MealTicketOrderStatus::PAID];
                break;
            case 2:
                $statusList = [MealTicketOrderStatus::MERCHANT_APPROVED];
                break;
            case 3:
                $statusList = [MealTicketOrderStatus::FINISHED];
                break;
            case 4:
                $statusList = [
                    MealTicketOrderStatus::REFUNDING,
                    MealTicketOrderStatus::REFUNDED
                ];
                break;
            default:
                $statusList = [
                    MealTicketOrderStatus::PAID,
                    MealTicketOrderStatus::REFUNDING,
                    MealTicketOrderStatus::REFUNDED,
                    MealTicketOrderStatus::MERCHANT_REJECTED,
                    MealTicketOrderStatus::MERCHANT_APPROVED,
                    MealTicketOrderStatus::CONFIRMED,
                    MealTicketOrderStatus::AUTO_CONFIRMED,
                    MealTicketOrderStatus::ADMIN_CONFIRMED,
                    MealTicketOrderStatus::FINISHED,
                    MealTicketOrderStatus::AUTO_FINISHED,
                ];
                break;
        }

        return $statusList;
    }

    private function handleOrderList($orderList)
    {
        $orderIds = $orderList->pluck('id')->toArray();
        $ticketList = OrderMealTicketService::getInstance()->getListByOrderIds($orderIds)->keyBy('order_id');
        return $orderList->map(function (MealTicketOrder $order) use ($ticketList) {
            /** @var OrderMealTicket $ticket */
            $ticket = $ticketList->get($order->id);
            $ticket->use_time_list = json_decode($ticket->use_time_list) ?: [];
            $ticket->inapplicable_products = json_decode($ticket->inapplicable_products) ?: [];
            $ticket->use_rules = json_decode($ticket->use_rules) ?: [];

            return [
                'id' => $order->id,
                'status' => $order->status,
                'statusDesc' => MealTicketOrderStatus::TEXT_MAP[$order->status],
                'ticketInfo' => $ticket,
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
        $order = MealTicketOrderService::getInstance()->getShopOrder($shopId, $orderId, $columns);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }

        $ticket = OrderMealTicketService::getInstance()->getTicketByOrderId($order->id);
        $ticket->use_time_list = json_decode($ticket->use_time_list) ?: [];
        $ticket->inapplicable_products = json_decode($ticket->inapplicable_products) ?: [];
        $ticket->use_rules = json_decode($ticket->use_rules) ?: [];
        $order['ticketInfo'] = $ticket;

        return $this->success($order);
    }

    public function approve()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $orderId = $this->verifyRequiredId('orderId');
        MealTicketOrderService::getInstance()->approve($shopId, $orderId);
        return $this->success();
    }

    public function refund()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $orderId = $this->verifyRequiredId('orderId');
        MealTicketOrderService::getInstance()->shopRefund($shopId, $orderId);
        return $this->success();
    }

    public function verify()
    {
        $code = $this->verifyRequiredString('code');

        $verifyCodeInfo = MealTicketVerifyService::getInstance()->getByCode($code);
        if (is_null($verifyCodeInfo)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '无效核销码');
        }

        $order = MealTicketOrderService::getInstance()->getPaidOrderById($verifyCodeInfo->order_id);
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
            MealTicketVerifyService::getInstance()->verify($verifyCodeInfo, $this->userId());
            MealTicketOrderService::getInstance()->userConfirm($order->user_id, $order->id);
        });

        return $this->success();
    }
}
