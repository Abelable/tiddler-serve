<?php

namespace App\Models;

/**
 * App\Models\ShopRestaurant
 *
 * @property int $id
 * @property int $shop_id 店铺id
 * @property int $restaurant_id 餐饮门店id
 * @property int $status 申请状态：0-待审核，1-审核通过，2-审核失败
 * @property string $failure_reason 审核失败原因
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRestaurant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRestaurant newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShopRestaurant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRestaurant query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRestaurant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRestaurant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRestaurant whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRestaurant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRestaurant whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRestaurant whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRestaurant whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRestaurant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ShopRestaurant withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShopRestaurant withoutTrashed()
 * @mixin \Eloquent
 */
class ShopRestaurant extends BaseModel
{
}
