<?php

namespace App\Models;

/**
 * App\Models\HotelProviderOrder
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
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProviderOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProviderOrder newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelProviderOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProviderOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProviderOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProviderOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProviderOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProviderOrder whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProviderOrder wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProviderOrder wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProviderOrder whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProviderOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProviderOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelProviderOrder whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|HotelProviderOrder withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelProviderOrder withoutTrashed()
 * @mixin \Eloquent
 */
class HotelProviderOrder extends BaseModel
{
}
