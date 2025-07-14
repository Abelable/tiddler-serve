<?php

namespace App\Models;

/**
 * App\Models\HotelShopDepositPaymentLog
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
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositPaymentLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositPaymentLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelShopDepositPaymentLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositPaymentLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositPaymentLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositPaymentLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositPaymentLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositPaymentLog whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositPaymentLog wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositPaymentLog wherePayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositPaymentLog wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositPaymentLog whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositPaymentLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositPaymentLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositPaymentLog whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|HotelShopDepositPaymentLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelShopDepositPaymentLog withoutTrashed()
 * @mixin \Eloquent
 */
class HotelShopDepositPaymentLog extends BaseModel
{
}
