<?php

namespace App\Models;

/**
 * App\Models\HotelShop
 *
 * @property int $id
 * @property int $status 状态：0-未支付保证金，1-已支付保证金
 * @property int $user_id 用户id
 * @property int $provider_id 服务商id
 * @property string $name 店铺名称
 * @property int $type 店铺类型：1-酒店官方，2-专营店，3-平台自营
 * @property string $cover 店铺封面图片
 * @property string $avatar 店铺头像
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelShop onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|HotelShop withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelShop withoutTrashed()
 * @mixin \Eloquent
 */
class HotelShop extends BaseModel
{
}
