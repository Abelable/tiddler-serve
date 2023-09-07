<?php

namespace App\Services;

use App\Models\HotelOrder;
use App\Utils\CodeResponse;
use App\Utils\Enums\HotelOrderEnums;
use App\Utils\Inputs\CreateHotelOrderInput;
use App\Utils\Inputs\PageInput;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HotelOrderService extends BaseService
{
    public function getOrderListByStatus($userId, $statusList, PageInput $input, $columns = ['*'])
    {
        $query = HotelOrder::query()->where('user_id', $userId);
        if (count($statusList) != 0) {
            $query = $query->whereIn('status', $statusList);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getShopOrderList($shopId, $statusList, PageInput $input, $columns = ['*'])
    {
        $query = HotelOrder::query()->where('shop_id', $shopId);
        if (count($statusList) != 0) {
            $query = $query->whereIn('status', $statusList);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getOrderById($userId, $id, $columns = ['*'])
    {
        return HotelOrder::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getUnpaidOrder(int $userId, $orderId, $columns = ['*'])
    {
        return HotelOrder::query()
            ->where('user_id', $userId)
            ->where('id', $orderId)
            ->where('status', HotelOrderEnums::STATUS_CREATE)
            ->first($columns);
    }

    public function getUnpaidOrderBySn($orderSn, $columns = ['*'])
    {
        return HotelOrder::query()
            ->where('order_sn', $orderSn)
            ->where('status', HotelOrderEnums::STATUS_CREATE)
            ->first($columns);
    }

    public function generateOrderSn()
    {
        return retry(5, function () {
            $orderSn = date('YmdHis') . rand(100000, 999999);
            if ($this->isOrderSnExists($orderSn)) {
                Log::warning('当前订单号已存在，orderSn：' . $orderSn);
                $this->throwBusinessException(CodeResponse::FAIL, '订单号生成失败');
            }
            return $orderSn;
        });
    }

    public function isOrderSnExists(string $orderSn)
    {
        return HotelOrder::query()->where('order_sn', $orderSn)->exists();
    }

    public function createOrder($userId, CreateHotelOrderInput $input)
    {
        list($paymentAmount, $averagePrice) = $this->calcPaymentAmount(
            $input->roomId,
            $input->checkInDate,
            $input->checkOutDate,
            $input->num
        );
        $room = HotelRoomService::getInstance()->getRoomById($input->roomId);
        $shop = HotelShopService::getInstance()->getShopById($room->shop_id);

        $order = HotelOrder::new();
        $order->order_sn = $this->generateOrderSn();
        $order->status = HotelOrderEnums::STATUS_CREATE;
        $order->user_id = $userId;
        $order->consignee = $input->consignee;
        $order->mobile = $input->mobile;
        $order->shop_id = $shop->id;
        $order->shop_avatar = $shop->avatar;
        $order->shop_name = $shop->name;
        $order->payment_amount = $paymentAmount;
        $order->refund_amount = $order->payment_amount;
        $order->save();

        // 生成订单房间快照
        $type = HotelRoomTypeService::getInstance()->getTypeById($room->type_id, ['*'], false);
        $hotel = HotelService::getInstance()->getHotelById($room->hotel_id);
        HotelOrderRoomService::getInstance()->createOrderRoom(
            $order->id,
            $hotel,
            $type,
            $room,
            $input,
            $averagePrice
        );

        // 设置订单支付超时任务
        // dispatch(new OverTimeCancelOrder($userId, $order->id));

        return $order->id;
    }

    public function calcPaymentAmount($roomId, $checkInDate, $checkOutDate, $num)
    {
        $room = HotelRoomService::getInstance()->getRoomById($roomId, ['price_list']);
        if (is_null($room)) {
            $this->throwBadArgumentValue();
        }
        $priceList = json_decode($room->price_list);

        $dateList = $this->createDateList($checkInDate, $checkOutDate);

        $datePriceList = $dateList->map(function ($date) use ($priceList) {
            $priceUnit = array_filter($priceList, function ($item) use ($date) {
                return $date >= $item->startDate && $date <= $item->endDate;
            })[0];
            return [
                'date' => $date,
                'price' => $priceUnit->price
            ];
        });

        $paymentAmount = (float)bcmul($datePriceList->pluck('price')->sum(), $num, 2);
        $averagePrice = round($datePriceList->avg('price'), 2);

        return [$paymentAmount, $averagePrice, $datePriceList];
    }

    private function createDateList($checkInDate, $checkOutDate)
    {
        $startDate = Carbon::createFromTimestamp($checkInDate);
        $endDate = Carbon::createFromTimestamp($checkOutDate);

        $dateList = collect($startDate->range($endDate))->map(function ($date) {
            return $date->timestamp;
        });
        $dateList->pop();

        return $dateList;
    }

    public function createWxPayOrder($userId, $orderId, $openid)
    {
        /** @var HotelOrder $order */
        $order = $this->getUnpaidOrder($userId, $orderId);
        if (is_null($order)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '订单不存在');
        }

        return [
            'out_trade_no' => time(),
            'body' => 'hotel_order_sn:' . $order->order_sn,
            'total_fee' => bcmul($order->payment_amount, 100),
            'openid' => $openid
        ];
    }

    public function wxPaySuccess(array $data)
    {
        $orderSn = $data['body'] ? str_replace('hotel_order_sn:', '', $data['body']) : '';
        $payId = $data['transaction_id'] ?? '';
        $actualPaymentAmount = $data['total_fee'] ? bcdiv($data['total_fee'], 100, 2) : 0;

        /** @var HotelOrder $order */
        $order = $this->getUnpaidOrderBySn($orderSn);

        if (bccomp($actualPaymentAmount, $order->payment_amount, 2) != 0) {
            $errMsg = "支付回调，订单{$data['body']}金额不一致，请检查，支付回调金额：{$actualPaymentAmount}，订单总金额：{$order->payment_amount}";
            Log::error($errMsg);
            $this->throwBusinessException(CodeResponse::FAIL, $errMsg);
        }

        $order->pay_id = $payId;
        $order->pay_time = now()->toDateTimeString();
        $order->status = HotelOrderEnums::STATUS_PAY;
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }
        // todo 通知（邮件或钉钉）管理员、
        // todo 通知（短信、系统消息）商家
        return $order;
    }

    public function userCancel($userId, $orderId)
    {
        return DB::transaction(function () use ($userId, $orderId) {
            return $this->cancel($userId, $orderId);
        });
    }

    public function systemCancel($userId, $orderId)
    {
        return DB::transaction(function () use ($userId, $orderId) {
            return $this->cancel($userId, $orderId, 'system');
        });
    }

    public function cancel($userId, $orderId, $role = 'user')
    {
        $order = $this->getOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if ($order->status != HotelOrderEnums::STATUS_CREATE) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能取消');
        }
        switch ($role) {
            case 'system':
                $order->status = HotelOrderEnums::STATUS_AUTO_CANCEL;
                break;
            case 'admin':
                $order->status = HotelOrderEnums::STATUS_ADMIN_CANCEL;
                break;
            case 'user':
                $order->status = HotelOrderEnums::STATUS_CANCEL;
                break;
        }
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }

        return $order;
    }

    public function confirm($userId, $orderId, $isAuto = false)
    {
        $order = $this->getOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }

        $order->status = $isAuto ? HotelOrderEnums::STATUS_AUTO_CONFIRM : HotelOrderEnums::STATUS_CONFIRM;
        $order->confirm_time = now()->toDateTimeString();
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }

        // todo 设置7天之后打款商家的定时任务，并通知管理员及商家。中间有退货的，取消定时任务。

        return $order;
    }

    public function delete($userId, $orderId)
    {
        $order = $this->getOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canDeleteHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能删除');
        }

        OrderGoodsService::getInstance()->delete($order->id);
        $order->delete();
    }

    public function refund($userId, $orderId)
    {
        $order = $this->getOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canRefundHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '该订单不能申请退款');
        }

        $order->status = HotelOrderEnums::STATUS_REFUND;

        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }

        // todo 通知商家
        // todo 开启自动退款定时任务

        return $order;
    }
}
