<?php

namespace App\Models\Theme;

use App\Models\BaseModel;

/**
 * App\Models\LakeCycle
 *
 * @property int $id
 * @property int $route_id 路线id
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
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycle newQuery()
 * @method static \Illuminate\Database\Query\Builder|LakeCycle onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycle query()
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycle whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycle whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycle whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycle whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycle whereRouteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycle whereScenicCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycle whereScenicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycle whereScenicName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycle whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycle whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|LakeCycle withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LakeCycle withoutTrashed()
 * @mixin \Eloquent
 */
class LakeCycle extends BaseModel
{
}
