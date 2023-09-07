<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\HotelOrder;
use App\Models\HotelOrderRoom;
use App\Services\HotelOrderRoomService;
use App\Services\HotelOrderService;
use App\Utils\CodeResponse;
use App\Utils\Enums\HotelOrderEnums;
use App\Utils\Inputs\CreateHotelOrderInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yansongda\LaravelPay\Facades\Pay;

class HotelOrderController extends Controller
{
    public function paymentAmount()
    {
        $roomId = $this->verifyRequiredId('roomId');
        $checkInDate = $this->verifyRequiredInteger('checkInDate');
        $checkOutDate = $this->verifyRequiredInteger('checkOutDate');
        $num = $this->verifyRequiredInteger('num');

        list($paymentAmount) = HotelOrderService::getInstance()->calcPaymentAmount($roomId, $checkInDate, $checkOutDate, $num);

        return $this->success($paymentAmount);
    }

    public function submit()
    {
        /** @var CreateHotelOrderInput $input */
        $input = CreateHotelOrderInput::new();

        // 分布式锁，防止重复请求
        $lockKey = sprintf('create_hotel_order_%s_%s', $this->userId(), md5(serialize($input)));
        $lock = Cache::lock($lockKey, 5);
        if (!$lock->get()) {
            $this->fail(CodeResponse::FAIL, '请勿重复提交订单');
        }

        $orderId = DB::transaction(function () use ($input) {
            return HotelOrderService::getInstance()->createOrder($this->userId(), $input);
        });

        return $this->success($orderId);
    }

    public function payParams()
    {
        $orderId = $this->verifyRequiredInteger('orderId');
        $order = HotelOrderService::getInstance()->createWxPayOrder($this->userId(), $orderId, $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($order);
        return $this->success($payParams);
    }

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');

        $statusList = $this->statusList($status);
        $page = HotelOrderService::getInstance()->getOrderListByStatus($this->userId(), $statusList, $input);
        $list = $this->orderList($page);

        return $this->success($this->paginate($page, $list));
    }

    public function shopList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');
        $shopId = $this->verifyId('shopId');

        $statusList = $this->statusList($status);
        $page = HotelOrderService::getInstance()->getShopOrderList($shopId, $statusList, $input);
        $list = $this->orderList($page);

        return $this->success($this->paginate($page, $list));
    }

    private function statusList($status)
    {
        switch ($status) {
            case 1:
                $statusList = [HotelOrderEnums::STATUS_CREATE];
                break;
            case 2:
                $statusList = [HotelOrderEnums::STATUS_PAY];
                break;
            case 3:
                $statusList = [HotelOrderEnums::STATUS_SETTLE_IN];
                break;
            case 4:
                $statusList = [HotelOrderEnums::STATUS_CONFIRM, HotelOrderEnums::STATUS_AUTO_CONFIRM];
                break;
            case 5:
                $statusList = [HotelOrderEnums::STATUS_REFUND, HotelOrderEnums::STATUS_SUPPLIER_REFUND, HotelOrderEnums::STATUS_REFUND_CONFIRM];
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
        $roomList = HotelOrderRoomService::getInstance()->getListByOrderIds($orderIds)->keyBy('order_id');
        return $orderList->map(function (HotelOrder $order) use ($roomList) {
            /** @var HotelOrderRoom $room */
            $room = $roomList->get($order->id);
            $room->image_list = json_decode($room->image_list);
            $room->facility_list = json_decode($room->facility_list);

            return [
                'id' => $order->id,
                'status' => $order->status,
                'statusDesc' => HotelOrderEnums::STATUS_TEXT_MAP[$order->status],
                'shopId' => $order->shop_id,
                'shopAvatar' => $order->shop_avatar,
                'shopName' => $order->shop_name,
                'roomInfo' => $room,
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
        HotelOrderService::getInstance()->userCancel($this->userId(), $id);
        return $this->success();
    }

    public function confirm()
    {
        $id = $this->verifyRequiredId('id');
        HotelOrderService::getInstance()->confirm($this->userId(), $id);
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        DB::transaction(function () use ($id) {
            HotelOrderService::getInstance()->delete($this->userId(), $id);
        });
        return $this->success();
    }

    public function refund()
    {
        $id = $this->verifyRequiredId('id');
        HotelOrderService::getInstance()->refund($this->userId(), $id);
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
            'shop_id',
            'shop_avatar',
            'shop_name',
            'payment_amount',
            'pay_time',
            'confirm_time',
            'created_at',
            'updated_at',
        ];
        $order = HotelOrderService::getInstance()->getOrderById($this->userId(), $id, $columns);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }
        $room = HotelOrderRoomService::getInstance()->getRoomByOrderId($order->id);
        $room->image_list = json_decode($room->image_list);
        $room->facility_list = json_decode($room->facility_list);
        $order['roomInfo'] = $room;
        return $this->success($order);
    }
}
