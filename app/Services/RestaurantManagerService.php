<?php

namespace App\Services;

use App\Models\RestaurantManager;

class RestaurantManagerService extends BaseService
{
    public function createManager($restaurantId, $userId)
    {
        $address = RestaurantManager::new();
        $address->restaurant_id = $restaurantId;
        $address->user_id = $userId;
        $address->save();
        return $address;
    }

    public function getManagerList($restaurantId, $columns = ['*'])
    {
        return RestaurantManager::query()->where('restaurant_id', $restaurantId)->get($columns);
    }

    public function deleteManager($restaurantId)
    {
        RestaurantManager::query()->where('restaurant_id', $restaurantId)->delete();
    }
}
