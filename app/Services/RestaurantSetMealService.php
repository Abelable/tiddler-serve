<?php

namespace App\Services;

use App\Models\RestaurantSetMeal;

class RestaurantSetMealService extends BaseService
{
    public function createRestaurantSetMeals($setMealId, array $restaurantIds)
    {
        foreach ($restaurantIds as $restaurantId) {
            $scenic = RestaurantSetMeal::new();
            $scenic->restaurant_id = $restaurantId;
            $scenic->set_meal_id = $setMealId;
            $scenic->save();
        }
    }

    public function updateRestaurantSetMeals($setMealId, array $restaurantIds)
    {
        $this->deleteBySetMealId($setMealId);
        $this->createRestaurantSetMeals($setMealId, $restaurantIds);
    }

    public function getListBySetMealId($setMealId, $columns = ['*'])
    {
        return RestaurantSetMeal::query()->where('set_meal_id', $setMealId)->get($columns);
    }

    public function getListByRestaurantId($restaurantId, $columns = ['*'])
    {
        return RestaurantSetMeal::query()->where('restaurant_id', $restaurantId)->get($columns);
    }

    public function deleteBySetMealId($setMealId)
    {
        RestaurantSetMeal::query()->where('set_meal_id', $setMealId)->delete();
    }
}
