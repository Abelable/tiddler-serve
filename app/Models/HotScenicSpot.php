<?php

namespace App\Models;

/**
 * App\Models\HotScenicSpot
 *
 * @property int $id
 * @property int $scenic_id 景点id
 * @property int $scenic_cover 景点封面
 * @property string $scenic_name 景点名称
 * @property string $recommend_reason 推荐理由
 * @property int $interested_user_number 感兴趣人数
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|HotScenicSpot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotScenicSpot newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotScenicSpot onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotScenicSpot query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotScenicSpot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotScenicSpot whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotScenicSpot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotScenicSpot whereInterestedUserNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotScenicSpot whereRecommendReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotScenicSpot whereScenicCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotScenicSpot whereScenicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotScenicSpot whereScenicName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotScenicSpot whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotScenicSpot whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|HotScenicSpot withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotScenicSpot withoutTrashed()
 * @mixin \Eloquent
 */
class HotScenicSpot extends BaseModel
{
}
