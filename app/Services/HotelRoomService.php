<?php

namespace App\Services;

use App\Models\HotelRoom;
use App\Utils\Inputs\Admin\HotelRoomListInput;
use App\Utils\Inputs\HotelRoomInput;
use App\Utils\Inputs\StatusPageInput;

class HotelRoomService extends BaseService
{
    public function getList(HotelRoomListInput $input, $columns=['*'])
    {
        $query = HotelRoom::query()->whereIn('status', [0, 1, 2]);
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->hotelId)) {
            $query = $query->where('hotel_id', $input->hotelId);
        }
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getListByHotelId($hotelId, $columns=['*'])
    {
        return HotelRoom::query()->where('hotel_id', $hotelId)->where('status', 1)->get($columns);
    }

    public function getListTotal($userId, $status)
    {
        return HotelRoom::query()->where('user_id', $userId)->where('status', $status)->count();
    }

    public function getRoomListByStatus($userId, StatusPageInput $input, $columns=['*'])
    {
        return HotelRoom::query()
            ->where('user_id', $userId)
            ->where('status', $input->status)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getRoomById($id, $columns=['*'])
    {
        return HotelRoom::query()->find($id, $columns);
    }

    public function getUserRoom($userId, $id, $columns=['*'])
    {
        return HotelRoom::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function createRoom($userId, $providerId, $shopId, HotelRoomInput $input)
    {
        $room = HotelRoom::new();
        $room->user_id = $userId;
        $room->provider_id = $providerId;
        $room->shop_id = $shopId;

        return $this->updateRoom($room, $input);
    }

    public function updateRoom(HotelRoom $room, HotelRoomInput $input)
    {
        if ($room->status == 2) {
            $room->status = 0;
            $room->failure_reason = '';
        }
        $room->hotel_id = $input->hotelId;
        $room->type_id = $input->typeId;
        $room->sales_commission_rate = $input->salesCommissionRate ?: 0;
        $room->promotion_commission_rate = $input->promotionCommissionRate ?: 0;
        $room->promotion_commission_upper_limit = $input->promotionCommissionUpperLimit ?: 0;
        $room->superior_promotion_commission_rate = $input->superiorPromotionCommissionRate ?: 0;
        $room->superior_promotion_commission_upper_limit = $input->superiorPromotionCommissionUpperLimit ?: 0;
        $room->price = $input->price;
        $room->price_list = json_encode($input->priceList);
        $room->breakfast_num = $input->breakfastNum;
        $room->guest_num = $input->guestNum;
        $room->cancellable = $input->cancellable;
        $room->save();

        return $room;
    }
}
