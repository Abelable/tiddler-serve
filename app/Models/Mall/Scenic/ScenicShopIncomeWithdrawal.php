<?php

namespace App\Models\Mall\Scenic;

use App\Models\BaseModel;

/**
 * App\Models\Mall\Scenic\ScenicShopIncomeWithdrawal
 *
 * @property int $id
 * @property int $status 状态：0-待审核；1-提现成功; 2-提现失败;
 * @property string|null $failure_reason 提现失败原因
 * @property int $user_id 用户id
 * @property int $shop_type 店铺类型：1-企业，2-个人
 * @property int $shop_id 店铺id
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
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncomeWithdrawal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncomeWithdrawal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncomeWithdrawal query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncomeWithdrawal whereActualAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncomeWithdrawal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncomeWithdrawal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncomeWithdrawal whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncomeWithdrawal whereHandlingFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncomeWithdrawal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncomeWithdrawal wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncomeWithdrawal whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncomeWithdrawal whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncomeWithdrawal whereReviewerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncomeWithdrawal whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncomeWithdrawal whereShopType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncomeWithdrawal whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncomeWithdrawal whereTaxFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncomeWithdrawal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncomeWithdrawal whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncomeWithdrawal whereWithdrawAmount($value)
 * @mixin \Eloquent
 */
class ScenicShopIncomeWithdrawal extends BaseModel
{
}
