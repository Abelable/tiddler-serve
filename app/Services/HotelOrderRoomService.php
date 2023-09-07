<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\HotelOrderRoom;
use App\Models\HotelRoom;
use App\Models\HotelRoomType;
use App\Utils\Inputs\CreateHotelOrderInput;

class HotelOrderRoomService extends BaseService
{
    public function createOrderRoom(
        $orderId,
        Hotel $hotel,
        HotelRoomType $typeInfo,
        HotelRoom $roomInfo,
        CreateHotelOrderInput $input,
        $price
    )
    {
        $room = HotelOrderRoom::new();
        $room->order_id = $orderId;
        $room->hotel_id = $hotel->id;
        $room->hotel_name = $hotel->name;
        $room->room_id = $roomInfo->id;
        $room->breakfast_num = $roomInfo->breakfast_num;
        $room->guest_num = $roomInfo->guest_num;
        $room->cancellable = $roomInfo->cancellable;
        $room->type_id = $typeInfo->id;
        $room->type_name = $typeInfo->name;
        $room->image_list = $typeInfo->image_list;
        $room->bed_desc = $typeInfo->bed_desc;
        $room->area_size = $typeInfo->area_size;
        $room->floor_desc = $typeInfo->floor_desc;
        $room->facility_list = $typeInfo->facility_list;
        $room->check_in_date = $input->checkInDate;
        $room->check_out_date = $input->checkOutDate;
        $room->number = $input->num;
        $room->price = $price;
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
