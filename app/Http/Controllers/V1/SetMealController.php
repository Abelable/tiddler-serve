<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\SetMeal;
use App\Services\SetMealService;
use App\Services\SetMealRestaurantService;
use App\Utils\CodeResponse;

class SetMealController extends Controller
{
    protected $only = [];

    public function list()
    {
        $restaurantId = $this->verifyRequiredId('restaurantId');

        $setMealIds = SetMealRestaurantService::getInstance()->getListByRestaurantId($restaurantId)->pluck('set_meal_id')->toArray();
        $setMealList = SetMealService::getInstance()->getListByIds($setMealIds, [
            'cover',
            'name',
            'price',
            'original_price',
            'sales_volume',
            'package_details',
            'validity_days',
            'validity_start_time',
            'validity_end_time',
            'buy_limit',
            'per_table_usage_limit',
            'use_time_list',
            'need_pre_book',
            'use_rules'
        ]);

        $setMealList = $setMealList->map(function (SetMeal $setMeal) {
            $setMeal->package_details = json_decode($setMeal->package_details);
            $setMeal->use_time_list = json_decode($setMeal->use_time_list) ?: [];
            $setMeal->use_rules = json_decode($setMeal->use_rules) ?: [];
            return $setMeal;
        });

        return $this->success($setMealList);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $setMeal = SetMealService::getInstance()->getSetMealById($id);
        if (is_null($setMeal)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前套餐不存在');
        }

        $setMeal['restaurantIds'] = $setMeal->restaurantIds();
        $setMeal->package_details = json_decode($setMeal->package_details);
        $setMeal->use_time_list = json_decode($setMeal->use_time_list) ?: [];
        $setMeal->use_rules = json_decode($setMeal->use_rules) ?: [];

        return $this->success($setMeal);
    }
}
