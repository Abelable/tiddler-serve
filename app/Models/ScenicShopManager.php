<?php

namespace App\Models;

/**
 * App\Models\ScenicShopManager
 *
 * @property int $id
 * @property int $shop_id 店铺id
 * @property int $user_id 用户id
 * @property string $avatar 用户头像
 * @property string $nickname 用户昵称
 * @property int $role_id 管理员角色id：1-超级管理员，2-运营，3-核销员
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopManager newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicShopManager onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopManager query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopManager whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopManager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopManager whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopManager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopManager whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopManager whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopManager whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopManager whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopManager whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicShopManager withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicShopManager withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicShopManager extends BaseModel
{
}
