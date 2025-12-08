<?php

namespace App\Models\Mall\Catering;

use App\Models\BaseModel;

/**
 * App\Models\Catering\SetMealRestaurant
 *
 * @property int $id
 * @property int $set_meal_id 餐券id
 * @property int $restaurant_id 餐饮门店id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealRestaurant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealRestaurant newQuery()
 * @method static \Illuminate\Database\Query\Builder|SetMealRestaurant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealRestaurant query()
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealRestaurant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealRestaurant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealRestaurant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealRestaurant whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealRestaurant whereSetMealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealRestaurant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SetMealRestaurant withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SetMealRestaurant withoutTrashed()
 * @mixin \Eloquent
 */
class SetMealRestaurant extends BaseModel
{
}
