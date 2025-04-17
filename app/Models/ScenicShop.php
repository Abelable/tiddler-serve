<?php

namespace App\Models;

/**
 * App\Models\ScenicShop
 *
 * @property int $id
 * @property int $status 状态：0-未支付保证金，1-已支付保证金
 * @property int $user_id 用户id
 * @property int $provider_id 服务商id
 * @property string $name 店铺名称
 * @property int $type 店铺类型：1-景区官方，2-旅行社，3-平台自营
 * @property string $cover 店铺封面图片
 * @property string $logo 店铺logo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicShop onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicShop withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicShop withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicShop extends BaseModel
{
}
