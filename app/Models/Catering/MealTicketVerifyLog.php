<?php

namespace App\Models\Catering;

use App\Models\BaseModel;

/**
 * App\Models\Catering\MealTicketVerifyLog
 *
 * @property int $id
 * @property int $order_id 订单id
 * @property int $restaurant_id 核销餐馆id
 * @property int $verifier_id 核销人员id
 * @property string $verify_time 核销时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|MealTicketVerifyLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog whereVerifierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyLog whereVerifyTime($value)
 * @method static \Illuminate\Database\Query\Builder|MealTicketVerifyLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MealTicketVerifyLog withoutTrashed()
 * @mixin \Eloquent
 */
class MealTicketVerifyLog extends BaseModel
{
}
