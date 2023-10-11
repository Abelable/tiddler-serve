<?php

namespace App\Models;

/**
 * App\Models\OrderMealTicket
 *
 * @property int $id
 * @property int $order_id 订单id
 * @property string $restaurant_name 门店名称
 * @property int $ticket_id 代金券id
 * @property float $ticket_price 代金券售价
 * @property float $ticket_original_price 代金券抵扣价格
 * @property int $number 代金券数量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrderMealTicket onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereRestaurantName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereTicketOriginalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereTicketPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|OrderMealTicket withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrderMealTicket withoutTrashed()
 * @mixin \Eloquent
 */
class OrderMealTicket extends BaseModel
{
}
