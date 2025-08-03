<?php

namespace App\Models;

/**
 * App\Models\LakeCycleMedia
 *
 * @property int $id
 * @property int $media_type 媒体类型：1-视频游记，2-图文游记
 * @property int $media_id 媒体id
 * @property string $cover 封面
 * @property string $title 标题
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycleMedia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycleMedia newQuery()
 * @method static \Illuminate\Database\Query\Builder|LakeCycleMedia onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycleMedia query()
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycleMedia whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycleMedia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycleMedia whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycleMedia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycleMedia whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycleMedia whereMediaType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycleMedia whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycleMedia whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeCycleMedia whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|LakeCycleMedia withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LakeCycleMedia withoutTrashed()
 * @mixin \Eloquent
 */
class LakeCycleMedia extends BaseModel
{
}
