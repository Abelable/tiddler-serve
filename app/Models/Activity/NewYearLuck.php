<?php

namespace App\Models\Activity;

use App\Models\BaseModel;

/**
 * App\Models\Activity\NewYearLuck
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $task_id 任务id
 * @property string $task_date 任务日期（用于唯一约束）
 * @property string $desc 描述
 * @property int $type 类型：1-获取，2-消耗
 * @property int $score 福气值
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLuck newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLuck newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLuck query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLuck whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLuck whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLuck whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLuck whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLuck whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLuck whereTaskDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLuck whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLuck whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLuck whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearLuck whereUserId($value)
 * @mixin \Eloquent
 */
class NewYearLuck extends BaseModel
{
}
