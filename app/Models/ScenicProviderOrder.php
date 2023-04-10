<?php

namespace App\Models;

/**
 * App\Models\ScenicProviderOrder
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
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProviderOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProviderOrder newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicProviderOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProviderOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProviderOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProviderOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProviderOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProviderOrder whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProviderOrder wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProviderOrder wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProviderOrder whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProviderOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProviderOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProviderOrder whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicProviderOrder withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicProviderOrder withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicProviderOrder extends BaseModel
{
}
