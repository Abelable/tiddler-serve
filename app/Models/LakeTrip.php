<?php

namespace App\Models;

/**
 * App\Models\LakeTrip
 *
 * @property int $id
 * @property int $lake_id 湖区id
 * @property int $scenic_id 景点id
 * @property string $scenic_cover 景点封面
 * @property string $scenic_name 景点名称
 * @property string $desc 描述
 * @property string $distance 行程里数（km）
 * @property string $duration 行程时长（h）
 * @property string $time 最佳时间（月）
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|LakeTrip newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LakeTrip newQuery()
 * @method static \Illuminate\Database\Query\Builder|LakeTrip onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LakeTrip query()
 * @method static \Illuminate\Database\Eloquent\Builder|LakeTrip whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeTrip whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeTrip whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeTrip whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeTrip whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeTrip whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeTrip whereLakeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeTrip whereScenicCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeTrip whereScenicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeTrip whereScenicName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeTrip whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeTrip whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeTrip whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|LakeTrip withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LakeTrip withoutTrashed()
 * @mixin \Eloquent
 */
class LakeTrip extends BaseModel
{
}
