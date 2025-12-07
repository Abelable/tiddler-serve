<?php

namespace App\Models;

/**
 * App\Models\TaskRewardWithdrawal
 *
 * @property int $id
 * @property int $status 状态：0-待审核；1-提现成功; 2-提现失败;
 * @property string|null $failure_reason 提现失败原因
 * @property int $user_id 用户id
 * @property int $task_id 任务id
 * @property string $withdraw_amount 提现金额
 * @property string $tax_fee 税费
 * @property string $handling_fee 手续费
 * @property string $actual_amount 实际到账金额
 * @property int $path 提现方式：1-微信；2-银行卡；3-余额
 * @property string|null $remark 备注
 * @property int|null $reviewer_id 审核管理员ID
 * @property string|null $reviewed_at 审核时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRewardWithdrawal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRewardWithdrawal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRewardWithdrawal query()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRewardWithdrawal whereActualAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRewardWithdrawal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRewardWithdrawal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRewardWithdrawal whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRewardWithdrawal whereHandlingFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRewardWithdrawal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRewardWithdrawal wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRewardWithdrawal whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRewardWithdrawal whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRewardWithdrawal whereReviewerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRewardWithdrawal whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRewardWithdrawal whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRewardWithdrawal whereTaxFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRewardWithdrawal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRewardWithdrawal whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskRewardWithdrawal whereWithdrawAmount($value)
 * @mixin \Eloquent
 */
class TaskRewardWithdrawal extends BaseModel
{
}
