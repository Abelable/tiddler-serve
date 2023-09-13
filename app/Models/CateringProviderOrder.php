<?php

namespace App\Models;

/**
 * App\Models\CateringProviderOrder
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $provider_id 服务商id
 * @property string $order_sn 订单编号
 * @property int $status 订单状态：0-待支付，1-支付成功
 * @property string $payment_amount 支付金额
 * @property int $pay_id 支付id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProviderOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProviderOrder newQuery()
 * @method static \Illuminate\Database\Query\Builder|CateringProviderOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProviderOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProviderOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProviderOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProviderOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProviderOrder whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProviderOrder wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProviderOrder wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProviderOrder whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProviderOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProviderOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringProviderOrder whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|CateringProviderOrder withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CateringProviderOrder withoutTrashed()
 * @mixin \Eloquent
 */
class CateringProviderOrder extends BaseModel
{
}
