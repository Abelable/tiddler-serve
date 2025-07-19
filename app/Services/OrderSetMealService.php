<?php

namespace App\Services;

use App\Models\Catering\OrderSetMeal;
use App\Models\Catering\SetMeal;

class OrderSetMealService extends BaseService
{
    public function createOrderSetMeal(
        $orderId,
        $number,
        SetMeal $setMealInfo
    )
    {
        $setMeal = OrderSetMeal::new();
        $setMeal->order_id = $orderId;
        $setMeal->number = $number;
        $setMeal->set_meal_id = $setMealInfo->id;
        $setMeal->cover = $setMealInfo->cover;
        $setMeal->name = $setMealInfo->name;
        $setMeal->price = $setMealInfo->price;
        $setMeal->original_price = $setMealInfo->original_price;
        $setMeal->package_details = $setMealInfo->package_details;
        $setMeal->validity_days = $setMealInfo->validity_days;
        $setMeal->validity_start_time = $setMealInfo->validity_start_time;
        $setMeal->validity_end_time = $setMealInfo->validity_end_time;
        $setMeal->buy_limit = $setMealInfo->buy_limit;
        $setMeal->per_table_usage_limit = $setMealInfo->per_table_usage_limit;
        $setMeal->use_time_list = $setMealInfo->use_time_list;
        $setMeal->need_pre_book = $setMealInfo->need_pre_book;
        $setMeal->use_rules = $setMealInfo->use_rules;
        $setMeal->save();
    }

    public function getSetMealByOrderId($orderId, $columns = ['*'])
    {
        return OrderSetMeal::query()->where('order_id', $orderId)->first($columns);
    }

    public function getListByOrderIds(array $orderIds, $columns = ['*'])
    {
        return OrderSetMeal::query()->whereIn('order_id', $orderIds)->get($columns);
    }

    public function getListByOrderIdsAndSetMealIds(array $orderIds, array $setMealIds, $columns = ['*'])
    {
        return OrderSetMeal::query()
            ->whereIn('order_id', $orderIds)
            ->whereIn('set_meal_id', $setMealIds)
            ->get($columns);
    }

    public function delete($orderId)
    {
        return OrderSetMeal::query()->where('order_id', $orderId)->delete();
    }
}
