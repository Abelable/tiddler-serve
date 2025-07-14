<?php

namespace App\Services;

use App\Models\HotelManager;

class HotelManagerService extends BaseService
{
    public function createManager($hotelId, $managerId)
    {
        $address = HotelManager::new();
        $address->hotel_id = $hotelId;
        $address->manager_id = $managerId;
        $address->save();
        return $address;
    }

    public function getListByHotelId($hotelId, $columns = ['*'])
    {
        return HotelManager::query()->where('hotel_id', $hotelId)->get($columns);
    }

    public function getListByManagerId($managerId, $columns = ['*'])
    {
        return HotelManager::query()->where('manager_id', $managerId)->get($columns);
    }

    public function deleteManager($hotelId)
    {
        HotelManager::query()->where('hotel_id', $hotelId)->delete();
    }
}
