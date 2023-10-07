<?php

namespace App\Models;

/**
 * App\Models\RestaurantTicket
 *
 * @property int $id
 * @property int $restaurant_id 餐饮门店id
 * @property int $ticket_id 优惠券id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantTicket newQuery()
 * @method static \Illuminate\Database\Query\Builder|RestaurantTicket onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantTicket whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantTicket whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantTicket whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantTicket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|RestaurantTicket withTrashed()
 * @method static \Illuminate\Database\Query\Builder|RestaurantTicket withoutTrashed()
 * @mixin \Eloquent
 */
class RestaurantTicket extends BaseModel
{
}
