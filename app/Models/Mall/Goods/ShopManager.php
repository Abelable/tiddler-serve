<?php

namespace App\Models\Mall\Goods;

use App\Models\BaseModel;

/**
 * App\Models\ShopManager
 *
 * @property int $id
 * @property int $shop_id 店铺id
 * @property int $role_id 管理员角色id
 * @property int $user_id 用户id
 * @property string $avatar 用户头像
 * @property string $nickname 用户昵称
 * @property string $mobile 联系方式
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManager newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShopManager onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManager query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManager whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManager whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManager whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManager whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopManager whereRoleId($value)
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
