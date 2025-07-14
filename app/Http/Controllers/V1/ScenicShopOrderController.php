<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ScenicOrder;
use App\Models\ScenicOrderTicket;
use App\Services\ScenicManagerService;
use App\Services\ScenicOrderService;
use App\Services\ScenicOrderTicketService;
use App\Services\ScenicOrderVerifyService;
use App\Services\ScenicShopManagerService;
use App\Utils\CodeResponse;
use App\Utils\Enums\ScenicOrderStatus;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

class ScenicShopOrderController extends Controller
{
    public function total()
    {
        $shopId = $this->verifyRequiredId('shopId');

        return $this->success([
            ScenicOrderService::getInstance()->getShopTotal($shopId, $this->statusList(1)),
            ScenicOrderService::getInstance()->getShopTotal($shopId, $this->statusList(2)),
            0,
            ScenicOrderService::getInstance()->getShopTotal($shopId, [ScenicOrderStatus::REFUNDING]),
        ]);
    }

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');
        $shopId = $this->verifyId('shopId');

        $statusList = $this->statusList($status);
        $page = ScenicOrderService::getInstance()->getShopOrderList($shopId, $statusList, $input);
        $orderList = collect($page->items());
        $list = $this->handleOrderList($orderList);

        return $this->success($this->paginate($page, $list));
    }

    private function statusList($status) {
        switch ($status) {
            case 1:
                $statusList = [ScenicOrderStatus::PAID];
                break;
            case 2:
                $statusList = [ScenicOrderStatus::MERCHANT_APPROVED];
                break;
            case 3:
                $statusList = [ScenicOrderStatus::FINISHED];
                break;
            case 4:
                $statusList = [
                    ScenicOrderStatus::REFUNDING,
                    ScenicOrderStatus::REFUNDED
                ];
                break;
            default:
                $statusList = [
                    ScenicOrderStatus::PAID,
                    ScenicOrderStatus::REFUNDING,
                    ScenicOrderStatus::REFUNDED,
                    ScenicOrderStatus::MERCHANT_REJECTED,
                    ScenicOrderStatus::MERCHANT_APPROVED,
                    ScenicOrderStatus::CONFIRMED,
                    ScenicOrderStatus::AUTO_CONFIRMED,
                    ScenicOrderStatus::ADMIN_CONFIRMED,
                    ScenicOrderStatus::FINISHED,
                    ScenicOrderStatus::AUTO_FINISHED,
                ];
                break;
        }

        return $statusList;
    }

    private function handleOrderList($orderList)
    {
        $orderIds = $orderList->pluck('id')->toArray();
        $ticketList = ScenicOrderTicketService::getInstance()
            ->getListByOrderIds($orderIds)
            ->keyBy('order_id');

        return $orderList->map(function (ScenicOrder $order) use ($ticketList) {
            /** @var ScenicOrderTicket $ticket */
            $ticket = $ticketList->get($order->id);

            return [
                'id' => $order->id,
                'orderSn' => $order->order_sn,
                'status' => $order->status,
                'statusDesc' => ScenicOrderStatus::TEXT_MAP[$order->status],
                'ticketInfo' => [
                    'id' => $ticket->ticket_id,
                    'name' => $ticket->name,
                    'categoryName' => $ticket->category_name,
                    'price' => $ticket->price,
                    'number' => $ticket->number,
                    'scenicList' => json_decode($ticket->scenic_list),
                    'validityTime' => $ticket->validity_time,
                    'selectedDateTimestamp' => $ticket->selected_date_timestamp,
                    'effectiveTime' => $ticket->effective_time,
                    'refundStatus' => $ticket->refund_status,
                    'needExchange' => $ticket->need_exchange
                ],
                'totalPrice' => $order->total_price,
                'paymentAmount' => $order->payment_amount,
                'consignee' => $order->consignee,
                'mobile' => $order->mobile,
                'idCardNumber' => $order->id_card_number,
                'payTime' => $order->pay_time,
                'createdAt' => $order->created_at
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
            'id_card_number',
            'total_price',
            'payment_amount',
            'approve_time',
            'pay_time',
            'confirm_time',
            'created_at',
            'updated_at',
        ];

        $order = ScenicOrderService::getInstance()->getShopOrder($shopId, $orderId, $columns);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }

        $ticket = ScenicOrderTicketService::getInstance()->getTicketByOrderId($order->id);
        $order['ticketInfo'] = [
            'id' => $ticket->ticket_id,
            'name' => $ticket->name,
            'categoryName' => $ticket->category_name,
            'price' => $ticket->price,
            'number' => $ticket->number,
            'scenicList' => json_decode($ticket->scenic_list),
            'validityTime' => $ticket->validity_time,
            'selectedDateTimestamp' => $ticket->selected_date_timestamp,
            'effectiveTime' => $ticket->effective_time,
            'refundStatus' => $ticket->refund_status,
            'needExchange' => $ticket->need_exchange
        ];

        return $this->success($order);
    }

    public function approve()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $orderId = $this->verifyRequiredId('orderId');
        ScenicOrderService::getInstance()->approve($shopId, $orderId);
        return $this->success();
    }

    public function refund()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $orderId = $this->verifyRequiredId('orderId');
        ScenicOrderService::getInstance()->shopRefund($shopId, $orderId);
        return $this->success();
    }

    public function verify()
    {
        $code = $this->verifyRequiredString('code');

        $verifyCodeInfo = ScenicOrderVerifyService::getInstance()->getByCode($code);
        if (is_null($verifyCodeInfo)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '无效核销码');
        }

        $order = ScenicOrderService::getInstance()->getApprovedOrderById($verifyCodeInfo->order_id);
        if (is_null($order)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '订单不存在');
        }

        $managerIds = ScenicManagerService::getInstance()
            ->getListByScenicId($verifyCodeInfo->scenic_id)->pluck('manager_id')->toArray();
        $managerUserIds = array_unique(ScenicShopManagerService::getInstance()
            ->getListByIds($managerIds)->pluck('user_id')->toArray());
        if ($order->shop_id != $this->user()->scenicShop->id && !in_array($this->userId(), $managerUserIds)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '非当前景点核销员，无法核销');
        }

        DB::transaction(function () use ($verifyCodeInfo, $order) {
            ScenicOrderVerifyService::getInstance()->verify($verifyCodeInfo, $this->userId());

            if (!ScenicOrderVerifyService::getInstance()->hasUnverifiedCodes($verifyCodeInfo->order_id)) {
                ScenicOrderService::getInstance()->userConfirm($order->user_id, $order->id);
            }
        });

        return $this->success();
    }
}
