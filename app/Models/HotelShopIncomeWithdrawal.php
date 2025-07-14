<?php

namespace App\Models;

/**
 * App\Models\HotelShopIncomeWithdrawal
 *
 * @property int $id
 * @property int $status 状态：0-待审核；1-提现成功; 2-提现失败;
 * @property string $failure_reason 提现失败原因
 * @property int $user_id 用户id
 * @property int $shop_id 店铺id
 * @property float $withdraw_amount 提现金额
 * @property float $tax_fee 税费
 * @property float $handling_fee 手续费
 * @property float $actual_amount 实际到账金额
 * @property int $path 提现方式：1-微信；2-银行卡；3-余额
 * @property string $remark 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelShopIncomeWithdrawal onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereActualAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereHandlingFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereTaxFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncomeWithdrawal whereWithdrawAmount($value)
 * @method static \Illuminate\Database\Query\Builder|HotelShopIncomeWithdrawal withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelShopIncomeWithdrawal withoutTrashed()
 * @mixin \Eloquent
 */
class HotelShopIncomeWithdrawal extends BaseModel
{
}
