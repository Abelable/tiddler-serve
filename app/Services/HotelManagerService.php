<?php

namespace App\Services;

use App\Models\HotelManager;

class HotelManagerService extends BaseService
{
    public function createManager($hotelId, $userId)
    {
        $address = HotelManager::new();
        $address->hotel_id = $hotelId;
        $address->user_id = $userId;
        $address->save();
        return $address;
    }

    public function getManagerList($hotelId, $columns = ['*'])
    {
        return HotelManager::query()->where('hotel_id', $hotelId)->get($columns);
    }

    public function deleteManager($hotelId)
    {
        HotelManager::query()->where('hotel_id', $hotelId)->delete();
    }
}
