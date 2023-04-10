<?php

namespace App\Models;

/**
 * App\Models\ScenicOpenTime
 *
 * @property int $id
 * @property int $scenic_id 景点id
 * @property string $open_date 开园日期
 * @property string $close_date 闭园日期
 * @property string $open_time 开园时间
 * @property string $close_time 闭园时间
 * @property string $tips 时间补充说明
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOpenTime newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOpenTime newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicOpenTime onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOpenTime query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOpenTime whereCloseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOpenTime whereCloseTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOpenTime whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOpenTime whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOpenTime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOpenTime whereOpenDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOpenTime whereOpenTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOpenTime whereScenicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOpenTime whereTips($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOpenTime whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicOpenTime withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicOpenTime withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicOpenTime extends BaseModel
{
}
