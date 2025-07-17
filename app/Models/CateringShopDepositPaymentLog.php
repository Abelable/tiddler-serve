<?php

namespace App\Models;

/**
 * App\Models\CateringShopDepositPaymentLog
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
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositPaymentLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositPaymentLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|CateringShopDepositPaymentLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositPaymentLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositPaymentLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositPaymentLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositPaymentLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositPaymentLog whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositPaymentLog wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositPaymentLog wherePayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositPaymentLog wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositPaymentLog whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositPaymentLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositPaymentLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositPaymentLog whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|CateringShopDepositPaymentLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CateringShopDepositPaymentLog withoutTrashed()
 * @mixin \Eloquent
 */
class CateringShopDepositPaymentLog extends BaseModel
{
}
