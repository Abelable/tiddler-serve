<?php

namespace App\Services\Mall\Catering;

use App\Models\Mall\Catering\SetMealRestaurant;
use App\Services\BaseService;

class SetMealRestaurantService extends BaseService
{
    public function create($setMealId, array $restaurantIds)
    {
        foreach ($restaurantIds as $restaurantId) {
            $scenic = SetMealRestaurant::new();
            $scenic->set_meal_id = $setMealId;
            $scenic->restaurant_id = $restaurantId;
            $scenic->save();
        }
    }

    public function update($setMealId, array $restaurantIds)
    {
        $this->deleteBySetMealId($setMealId);
        $this->create($setMealId, $restaurantIds);
    }

    public function getListBySetMealId($setMealId, $columns = ['*'])
    {
        return SetMealRestaurant::query()->where('set_meal_id', $setMealId)->get($columns);
    }

    public function getListByRestaurantId($restaurantId, $columns = ['*'])
    {
        return SetMealRestaurant::query()->where('restaurant_id', $restaurantId)->get($columns);
    }

    public function deleteBySetMealId($setMealId)
    {
        SetMealRestaurant::query()->where('set_meal_id', $setMealId)->delete();
    }
}
