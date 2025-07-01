<?php

namespace App\Models;

/**
 * App\Models\ShopManagerRole
 *
 * @property int $id
 * @property int $shop_id 店铺id
 * @property string $name 管理员角色名称
 * @property string $desc 管理员角色描述
 * @property string $permission 管理员角色权限
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManagerRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManagerRole newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShopManagerRole onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManagerRole query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManagerRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManagerRole whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManagerRole whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManagerRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManagerRole whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManagerRole wherePermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManagerRole whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManagerRole whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ShopManagerRole withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShopManagerRole withoutTrashed()
 * @mixin \Eloquent
 */
class ShopManagerRole extends BaseModel
{
}
