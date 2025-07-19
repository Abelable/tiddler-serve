<?php

namespace App\Services\Mall\Catering;

use App\Models\Catering\RestaurantManager;
use App\Services\BaseService;

class RestaurantManagerService extends BaseService
{
    public function createManager($restaurantId, $managerId)
    {
        $address = RestaurantManager::new();
        $address->restaurant_id = $restaurantId;
        $address->manager_id = $managerId;
        $address->save();
        return $address;
    }

    public function getListByRestaurantId($restaurantId, $columns = ['*'])
    {
        return RestaurantManager::query()->where('restaurant_id', $restaurantId)->get($columns);
    }

    public function getListByManagerId($managerId, $columns = ['*'])
    {
        return RestaurantManager::query()->where('manager_id', $managerId)->get($columns);
    }

    public function deleteManager($restaurantId)
    {
        RestaurantManager::query()->where('restaurant_id', $restaurantId)->delete();
    }
}
