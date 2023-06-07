<?php

namespace App\Models;

/**
 * App\Models\MerchantOrder
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $merchant_id 商家id
 * @property string $order_sn 订单编号
 * @property int $status 订单状态：0-待支付，1-支付成功
 * @property string $payment_amount 支付金额
 * @property int $pay_id 支付id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder newQuery()
 * @method static \Illuminate\Database\Query\Builder|MerchantOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantOrder whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|MerchantOrder withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MerchantOrder withoutTrashed()
 * @mixin \Eloquent
 */
class MerchantOrder extends BaseModel
{
}
