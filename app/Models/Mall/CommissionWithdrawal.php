<?php

namespace App\Models\Mall;

use App\Models\BaseModel;

/**
 * App\Models\CommissionWithdrawal
 *
 * @property int $id
 * @property int $status 状态：0-待审核；1-提现成功; 2-提现失败;
 * @property string|null $failure_reason 提现失败原因
 * @property int $user_id 用户id
 * @property int $scene 提现场景：1-自购佣金；2-分享佣金；3-团队佣金；
 * @property int $path 提现方式：1-微信；2-银行卡；3-余额
 * @property string $withdraw_amount 提现金额
 * @property string $tax_fee 税费
 * @property string $handling_fee 手续费
 * @property string $actual_amount 实际到账金额
 * @property string|null $remark 备注
 * @property int|null $reviewer_id 审核管理员ID
 * @property string|null $reviewed_at 审核时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionWithdrawal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionWithdrawal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionWithdrawal query()
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionWithdrawal whereActualAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionWithdrawal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionWithdrawal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionWithdrawal whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionWithdrawal whereHandlingFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionWithdrawal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionWithdrawal wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionWithdrawal whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionWithdrawal whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionWithdrawal whereReviewerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionWithdrawal whereScene($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionWithdrawal whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionWithdrawal whereTaxFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionWithdrawal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionWithdrawal whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CommissionWithdrawal whereWithdrawAmount($value)
 * @mixin \Eloquent
 */
class CommissionWithdrawal extends BaseModel
{
}
