<?php

namespace App\Models\Activity;

use App\Models\BaseModel;

/**
 * App\Models\Activity\NewYearUserTask
 *
 * @property int $id
 * @property int $status 任务状态：0-待完成，1-已完成
 * @property int $user_id 用户id
 * @property int $task_id 任务id
 * @property int|null $reference_id 外部参考ID，如商家id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserTask query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserTask whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserTask whereReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserTask whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserTask whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserTask whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserTask whereUserId($value)
 * @mixin \Eloquent
 */
class NewYearUserTask extends BaseModel
{
}
