<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\HotelOrderRoom;
use App\Models\HotelRoom;
use App\Models\HotelRoomType;

class HotelOrderRoomService extends BaseService
{
    public function createOrderRoom(
        $orderId,
        Hotel $hotel,
        HotelRoomType $typeInfo,
        $checkInDate,
        $checkOutDate,
        $price,
        $number,
        HotelRoom $roomInfo
    )
    {
        $room = HotelOrderRoom::new();
        $room->order_id = $orderId;
        $room->room_id = $roomInfo->id;
        $room->image = json_decode($typeInfo->image_list)[0];
        $room->type_id = $typeInfo->id;
        $room->type_name = $typeInfo->name;
        $room->hotel_id = $hotel->id;
        $room->hotel_name = $hotel->name;
        $room->check_in_date = $checkInDate;
        $room->check_out_date = $checkOutDate;
        $room->price = $price;
        $room->number = $number;
        $room->breakfast_num = $roomInfo->breakfast_num;
        $room->guest_num = $roomInfo->guest_num;
        $room->cancellable = $roomInfo->cancellable;
        $room->save();
    }

    public function getRoomByOrderId($orderId, $columns = ['*'])
    {
        return HotelOrderRoom::query()->where('order_id', $orderId)->first($columns);
    }

    public function getListByOrderIds(array $orderIds, $columns = ['*'])
    {
        return HotelOrderRoom::query()->whereIn('order_id', $orderIds)->get($columns);
    }

    public function delete($orderId)
    {
        return HotelOrderRoom::query()->where('order_id', $orderId)->delete();
    }
}
