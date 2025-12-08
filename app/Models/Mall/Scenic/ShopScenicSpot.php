<?php

namespace App\Models\Mall\Scenic;

use App\Models\BaseModel;

/**
 * App\Models\ShopScenicSpot
 *
 * @property int $id
 * @property int $shop_id 店铺id
 * @property int $scenic_id 景点id
 * @property int $status 申请状态：0-待审核，1-审核通过，2-审核失败
 * @property string $failure_reason 审核失败原因
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShopScenicSpot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopScenicSpot newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShopScenicSpot onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopScenicSpot query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopScenicSpot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopScenicSpot whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopScenicSpot whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopScenicSpot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopScenicSpot whereScenicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopScenicSpot whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopScenicSpot whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopScenicSpot whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ShopScenicSpot withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShopScenicSpot withoutTrashed()
 * @mixin \Eloquent
 */
class ShopScenicSpot extends BaseModel
{
}
