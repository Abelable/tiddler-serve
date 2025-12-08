<?php

namespace App\Services\Mall\Hotel;

use App\Models\Mall\Hotel\Hotel;
use App\Models\Mall\Hotel\HotelOrderRoom;
use App\Models\Mall\Hotel\HotelRoom;
use App\Models\Mall\Hotel\HotelRoomType;
use App\Services\BaseService;
use App\Utils\Inputs\HotelOrderInput;

class HotelOrderRoomService extends BaseService
{
    public function createOrderRoom(
        $userId,
        $orderId,
        Hotel $hotel,
        HotelRoomType $typeInfo,
        HotelRoom $roomInfo,
        HotelOrderInput $input,
        $price
    )
    {
        $room = HotelOrderRoom::new();
        $room->user_id = $userId;
        $room->order_id = $orderId;
        $room->hotel_id = $hotel->id;
        $room->hotel_name = $hotel->name;
        $room->type_id = $typeInfo->id;
        $room->type_name = $typeInfo->name;
        $room->room_id = $roomInfo->id;
        $room->check_in_date = $input->checkInDate;
        $room->check_out_date = $input->checkOutDate;
        $room->price = $price;
        $room->sales_commission_rate = $roomInfo->sales_commission_rate;
        $room->promotion_commission_rate = $roomInfo->promotion_commission_rate;
        $room->promotion_commission_upper_limit = $roomInfo->promotion_commission_upper_limit;
        $room->superior_promotion_commission_rate = $roomInfo->superior_promotion_commission_rate;
        $room->superior_promotion_commission_upper_limit = $roomInfo->superior_promotion_commission_upper_limit;
        $room->number = $input->num;
        $room->breakfast_num = $roomInfo->breakfast_num;
        $room->guest_num = $roomInfo->guest_num;
        $room->cancellable = $roomInfo->cancellable;
        $room->image_list = $typeInfo->image_list;
        $room->bed_desc = $typeInfo->bed_desc;
        $room->area_size = $typeInfo->area_size;
        $room->floor_desc = $typeInfo->floor_desc;
        $room->facility_list = $typeInfo->facility_list;
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

    public function getListByOrderIdsAndRoomIds(array $orderIds, array $roomIds,  $columns = ['*'])
    {
        return HotelOrderRoom::query()
            ->whereIn('order_id', $orderIds)
            ->whereIn('room_id', $roomIds)
            ->get($columns);
    }

    public function searchList($userId, $keyword, $columns = ['*'])
    {
        return HotelOrderRoom::query()
            ->where('user_id', $userId)
            ->where('hotel_name', 'like', "%{$keyword}%")
            ->get($columns);
    }

    public function delete($orderId)
    {
        return HotelOrderRoom::query()->where('order_id', $orderId)->delete();
    }
}
