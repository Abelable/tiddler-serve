<?php

namespace App\Models;

/**
 * App\Models\SetMealVerifyLog
 *
 * @property int $id
 * @property int $order_id 订单id
 * @property int $restaurant_id 核销餐馆id
 * @property int $verifier_id 核销人员id
 * @property string $verify_time 核销时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|SetMealVerifyLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog whereVerifierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyLog whereVerifyTime($value)
 * @method static \Illuminate\Database\Query\Builder|SetMealVerifyLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SetMealVerifyLog withoutTrashed()
 * @mixin \Eloquent
 */
class SetMealVerifyLog extends BaseModel
{
}
