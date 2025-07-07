<?php

namespace App\Models;

/**
 * App\Models\ScenicShopDepositPaymentLog
 *
 * @property int $id
 * @property int $status 状态：0-待支付，1-支付成功
 * @property int $user_id 用户id
 * @property int $provider_id 服务商id
 * @property int $shop_id 店铺id
 * @property float $payment_amount 支付金额
 * @property string $pay_id 微信支付id
 * @property string $pay_time 支付时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositPaymentLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositPaymentLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicShopDepositPaymentLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositPaymentLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositPaymentLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositPaymentLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositPaymentLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositPaymentLog wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositPaymentLog wherePayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositPaymentLog wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositPaymentLog whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositPaymentLog whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositPaymentLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositPaymentLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositPaymentLog whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicShopDepositPaymentLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicShopDepositPaymentLog withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicShopDepositPaymentLog extends BaseModel
{
}
