<?php

namespace App\Models\Mall\Catering;

use App\Models\BaseModel;

/**
 * App\Models\Catering\MealTicketRestaurant
 *
 * @property int $id
 * @property int $meal_ticket_id 餐券id
 * @property int $restaurant_id 餐饮门店id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketRestaurant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketRestaurant newQuery()
 * @method static \Illuminate\Database\Query\Builder|MealTicketRestaurant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketRestaurant query()
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketRestaurant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketRestaurant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketRestaurant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketRestaurant whereMealTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketRestaurant whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketRestaurant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|MealTicketRestaurant withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MealTicketRestaurant withoutTrashed()
 * @mixin \Eloquent
 */
class MealTicketRestaurant extends BaseModel
{
}
