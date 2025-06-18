<?php

namespace App\Models;

/**
 * App\Models\ShopDepositPaymentLog
 *
 * @property int $id
 * @property int $status 状态：0-待支付，1-支付成功
 * @property int $user_id 用户id
 * @property int $merchant_id 商家id
 * @property int $shop_id 店铺id
 * @property float $payment_amount 支付金额
 * @property string $pay_id 微信支付id
 * @property string $pay_time 支付时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositPaymentLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositPaymentLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShopDepositPaymentLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositPaymentLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositPaymentLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositPaymentLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositPaymentLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositPaymentLog whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositPaymentLog wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositPaymentLog wherePayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositPaymentLog wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositPaymentLog whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositPaymentLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositPaymentLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositPaymentLog whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ShopDepositPaymentLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShopDepositPaymentLog withoutTrashed()
 * @mixin \Eloquent
 */
class ShopDepositPaymentLog extends BaseModel
{
}
