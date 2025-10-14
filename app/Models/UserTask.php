<?php

namespace App\Models;

/**
 * App\Models\UserTask
 *
 * @property int $id
 * @property int $status 任务状态：1-进行中，2-已完成，待领取奖励，3-领取审核中，4-已领取，5-领取失败，6-已取消
 * @property int $step 任务进度
 * @property int $user_id 用户id
 * @property int $task_id 任务id
 * @property int $product_type 产品类型：1-景点，2-酒店，3-餐饮，4-电商
 * @property int $product_id 产品id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask newQuery()
 * @method static \Illuminate\Database\Query\Builder|UserTask onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereStep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|UserTask withTrashed()
 * @method static \Illuminate\Database\Query\Builder|UserTask withoutTrashed()
 * @mixin \Eloquent
 */
class UserTask extends BaseModel
{
}
