<?php

namespace App\Models;

/**
 * App\Models\RewardWithdrawal
 *
 * @property int $id
 * @property int $status 状态：0-待审核；1-提现成功; 2-提现失败;
 * @property string $failure_reason 提现失败原因
 * @property int $task_id 任务id
 * @property int $user_id 用户id
 * @property float $withdraw_amount 提现金额
 * @property float $tax_fee 税费
 * @property float $handling_fee 手续费
 * @property float $actual_amount 实际到账金额
 * @property int $path 提现方式：1-微信；2-银行卡；3-余额
 * @property string $remark 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|RewardWithdrawal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RewardWithdrawal newQuery()
 * @method static \Illuminate\Database\Query\Builder|RewardWithdrawal onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RewardWithdrawal query()
 * @method static \Illuminate\Database\Eloquent\Builder|RewardWithdrawal whereActualAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RewardWithdrawal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RewardWithdrawal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RewardWithdrawal whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RewardWithdrawal whereHandlingFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RewardWithdrawal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RewardWithdrawal wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RewardWithdrawal whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RewardWithdrawal whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RewardWithdrawal whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RewardWithdrawal whereTaxFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RewardWithdrawal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RewardWithdrawal whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RewardWithdrawal whereWithdrawAmount($value)
 * @method static \Illuminate\Database\Query\Builder|RewardWithdrawal withTrashed()
 * @method static \Illuminate\Database\Query\Builder|RewardWithdrawal withoutTrashed()
 * @mixin \Eloquent
 */
class RewardWithdrawal extends BaseModel
{
}
