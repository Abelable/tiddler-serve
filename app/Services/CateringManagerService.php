<?php

namespace App\Services;

use App\Models\CateringManager;

class CateringManagerService extends BaseService
{
    public function createManager($restaurantId, $managerId)
    {
        $address = CateringManager::new();
        $address->restaurant_id = $restaurantId;
        $address->manager_id = $managerId;
        $address->save();
        return $address;
    }

    public function getListByRestaurantId($restaurantId, $columns = ['*'])
    {
        return CateringManager::query()->where('restaurant_id', $restaurantId)->get($columns);
    }

    public function getListByManagerId($managerId, $columns = ['*'])
    {
        return CateringManager::query()->where('manager_id', $managerId)->get($columns);
    }

    public function deleteManager($restaurantId)
    {
        CateringManager::query()->where('restaurant_id', $restaurantId)->delete();
    }
}
