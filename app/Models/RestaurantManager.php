<?php

namespace App\Models;

/**
 * App\Models\RestaurantManager
 *
 * @property int $id
 * @property int $restaurant_id 餐馆id
 * @property int $user_id 用户id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantManager newQuery()
 * @method static \Illuminate\Database\Query\Builder|RestaurantManager onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantManager query()
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantManager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantManager whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantManager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantManager whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantManager whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantManager whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|RestaurantManager withTrashed()
 * @method static \Illuminate\Database\Query\Builder|RestaurantManager withoutTrashed()
 * @mixin \Eloquent
 */
class RestaurantManager extends BaseModel
{
}
