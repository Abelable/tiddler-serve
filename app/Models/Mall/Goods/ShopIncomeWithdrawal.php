<?php

namespace App\Models\Mall\Goods;

use App\Models\BaseModel;

/**
 * App\Models\ShopIncomeWithdrawal
 *
 * @property int $id
 * @property int $status 状态：0-待审核；1-提现成功; 2-提现失败;
 * @property string|null $failure_reason 提现失败原因
 * @property int $user_id 用户id
 * @property int $merchant_type 商家类型：1-景点，2-酒店，3-餐饮，4-电商
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
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal whereActualAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal whereHandlingFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal whereMerchantType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal whereReviewedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal whereReviewerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal whereShopType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal whereTaxFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncomeWithdrawal whereWithdrawAmount($value)
 * @mixin \Eloquent
 */
class ShopIncomeWithdrawal extends BaseModel
{
}
