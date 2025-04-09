<?php

namespace App\Models;

/**
 * App\Models\ShopManager
 *
 * @property int $id
 * @property int $shop_id 店铺id
 * @property int $user_id 用户id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManager newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShopManager onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManager query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManager whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManager whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManager whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManager whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ShopManager withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShopManager withoutTrashed()
 * @mixin \Eloquent
 */
class ShopManager extends BaseModel
{
}
