<?php

namespace App\Models\Mall\Hotel;

use App\Models\BaseModel;

/**
 * App\Models\Mall\Hotel\HotelShopIncomeWithdrawal
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
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereActualAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereHandlingFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereReviewerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereShopType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereTaxFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereWithdrawAmount($value)
 * @mixin \Eloquent
 */
class HotelShopIncomeWithdrawal extends BaseModel
{
}
