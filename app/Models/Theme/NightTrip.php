<?php

namespace App\Models\Theme;

use App\Models\BaseModel;

/**
 * App\Models\NightTrip
 *
 * @property int $id
 * @property int $scenic_id 景点id
 * @property string $scenic_cover 景点封面
 * @property string $scenic_name 景点名称
 * @property string $feature_tips 特色
 * @property string $recommend_tips 推荐
 * @property string $guide_tips 攻略
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|NightTrip newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NightTrip newQuery()
 * @method static \Illuminate\Database\Query\Builder|NightTrip onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NightTrip query()
 * @method static \Illuminate\Database\Eloquent\Builder|NightTrip whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NightTrip whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NightTrip whereFeatureTips($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NightTrip whereGuideTips($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NightTrip whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NightTrip whereRecommendTips($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NightTrip whereScenicCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NightTrip whereScenicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NightTrip whereScenicName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NightTrip whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NightTrip whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|NightTrip withTrashed()
 * @method static \Illuminate\Database\Query\Builder|NightTrip withoutTrashed()
 * @mixin \Eloquent
 */
class NightTrip extends BaseModel
{
}
