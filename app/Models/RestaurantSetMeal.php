<?php

namespace App\Models;

/**
 * App\Models\RestaurantSetMeal
 *
 * @property int $id
 * @property int $restaurant_id 餐饮门店id
 * @property int $set_meal_id 套餐id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantSetMeal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantSetMeal newQuery()
 * @method static \Illuminate\Database\Query\Builder|RestaurantSetMeal onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantSetMeal query()
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantSetMeal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantSetMeal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantSetMeal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantSetMeal whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantSetMeal whereSetMealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantSetMeal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|RestaurantSetMeal withTrashed()
 * @method static \Illuminate\Database\Query\Builder|RestaurantSetMeal withoutTrashed()
 * @mixin \Eloquent
 */
class RestaurantSetMeal extends BaseModel
{
}
