<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\HotelOrder;
use App\Models\HotelOrderRoom;
use App\Services\HotelManagerService;
use App\Services\HotelOrderService;
use App\Services\HotelOrderRoomService;
use App\Services\HotelOrderVerifyService;
use App\Services\HotelShopManagerService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Enums\HotelOrderStatus;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

class HotelShopOrderController extends Controller
{
    public function total()
    {
        $shopId = $this->verifyRequiredId('shopId');

        return $this->success([
            HotelOrderService::getInstance()->getShopTotal($shopId, $this->statusList(1)),
            HotelOrderService::getInstance()->getShopTotal($shopId, $this->statusList(2)),
            0,
            HotelOrderService::getInstance()->getShopTotal($shopId, [HotelOrderStatus::REFUNDING]),
        ]);
    }

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');
        $shopId = $this->verifyId('shopId');

        $statusList = $this->statusList($status);
        $page = HotelOrderService::getInstance()->getShopOrderList($shopId, $statusList, $input);
        $orderList = collect($page->items());
        $list = $this->handleOrderList($orderList);

        return $this->success($this->paginate($page, $list));
    }

    private function statusList($status) {
        switch ($status) {
            case 1:
                $statusList = [HotelOrderStatus::PAID];
                break;
            case 2:
                $statusList = [HotelOrderStatus::MERCHANT_APPROVED];
                break;
            case 3:
                $statusList = [HotelOrderStatus::FINISHED];
                break;
            case 4:
                $statusList = [
                    HotelOrderStatus::REFUNDING,
                    HotelOrderStatus::REFUNDED
                ];
                break;
            default:
                $statusList = [
                    HotelOrderStatus::PAID,
                    HotelOrderStatus::REFUNDING,
                    HotelOrderStatus::REFUNDED,
                    HotelOrderStatus::MERCHANT_REJECTED,
                    HotelOrderStatus::MERCHANT_APPROVED,
                    HotelOrderStatus::CONFIRMED,
                    HotelOrderStatus::AUTO_CONFIRMED,
                    HotelOrderStatus::ADMIN_CONFIRMED,
                    HotelOrderStatus::FINISHED,
                    HotelOrderStatus::AUTO_FINISHED,
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
        $roomList = HotelOrderRoomService::getInstance()
            ->getListByOrderIds($orderIds)
            ->keyBy('order_id');

        return $orderList->map(function (HotelOrder $order) use ($userList, $roomList) {
            $userInfo = $userList->get($order->user_id);

            /** @var HotelOrderRoom $room */
            $room = $roomList->get($order->id);
            $room->image_list = json_decode($room->image_list);
            $room->facility_list = json_decode($room->facility_list);

            return [
                'id' => $order->id,
                'orderSn' => $order->order_sn,
                'status' => $order->status,
                'statusDesc' => HotelOrderStatus::TEXT_MAP[$order->status],
                'userInfo' => $userInfo,
                'roomInfo' => $room,
                'totalPrice' => $order->total_price,
                'deduction_balance' => $order->deduction_balance,
                'paymentAmount' => $order->payment_amount,
                'consignee' => $order->consignee,
                'mobile' => $order->mobile,
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
            'user_id',
            'order_sn',
            'status',
            'consignee',
            'mobile',
            'total_price',
            'deduction_balance',
            'payment_amount',
            'pay_time',
            'confirm_time',
            'created_at',
            'updated_at',
        ];

        $order = HotelOrderService::getInstance()->getShopOrder($shopId, $orderId, $columns);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }

        $userInfo = UserService::getInstance()->getUserById($order->user_id);
        $order['userInfo'] = $userInfo;
        unset($order->user_id);

        $room = HotelOrderRoomService::getInstance()->getRoomByOrderId($order->id);
        $room->image_list = json_decode($room->image_list);
        $room->facility_list = json_decode($room->facility_list);
        $order['roomInfo'] = $room;

        return $this->success($order);
    }

    public function approve()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $orderId = $this->verifyRequiredId('orderId');
        HotelOrderService::getInstance()->approve($shopId, $orderId);
        return $this->success();
    }

    public function refund()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $orderId = $this->verifyRequiredId('orderId');
        HotelOrderService::getInstance()->shopRefund($shopId, $orderId);
        return $this->success();
    }

    public function verify()
    {
        $code = $this->verifyRequiredString('code');

        $verifyCodeInfo = HotelOrderVerifyService::getInstance()->getByCode($code);
        if (is_null($verifyCodeInfo)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '无效核销码');
        }

        $order = HotelOrderService::getInstance()->getApprovedOrderById($verifyCodeInfo->order_id);
        if (is_null($order)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '订单不存在');
        }

        $managerIds = HotelManagerService::getInstance()
            ->getListByHotelId($verifyCodeInfo->hotel_id)->pluck('manager_id')->toArray();
        $managerUserIds = array_unique(HotelShopManagerService::getInstance()
            ->getListByIds($managerIds)->pluck('user_id')->toArray());
        if ($order->shop_id != $this->user()->hotelShop->id && !in_array($this->userId(), $managerUserIds)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '非当前酒店核销员，无法核销');
        }

        DB::transaction(function () use ($verifyCodeInfo, $order) {
            HotelOrderVerifyService::getInstance()->verify($verifyCodeInfo, $this->userId());
            HotelOrderService::getInstance()->userConfirm($order->user_id, $order->id);
        });

        return $this->success();
    }
}
