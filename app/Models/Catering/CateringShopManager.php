<?php

namespace App\Models\Catering;

use App\Models\BaseModel;

/**
 * App\Models\CateringShopManager
 *
 * @property int $id
 * @property int $shop_id 店铺id
 * @property int $user_id 用户id
 * @property int $role_id 管理员角色id：1-超级管理员，2-运营，3-核销员
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopManager newQuery()
 * @method static \Illuminate\Database\Query\Builder|CateringShopManager onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopManager query()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopManager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopManager whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopManager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopManager whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopManager whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopManager whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopManager whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|CateringShopManager withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CateringShopManager withoutTrashed()
 * @mixin \Eloquent
 */
class CateringShopManager extends BaseModel
{
}
