<?php

namespace App\Models;

/**
 * App\Models\HotelShopManager
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
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopManager newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelShopManager onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopManager query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopManager whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopManager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopManager whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopManager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopManager whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopManager whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopManager whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopManager whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopManager whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopManager whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|HotelShopManager withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelShopManager withoutTrashed()
 * @mixin \Eloquent
 */
class HotelShopManager extends BaseModel
{
}
