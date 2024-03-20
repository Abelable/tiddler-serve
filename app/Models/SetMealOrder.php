<?php

namespace App\Models;

use App\Utils\Traits\SetMealOrderStatusTrait;

/**
 * App\Models\SetMealOrder
 *
 * @property int $id
 * @property string $order_sn 订单编号
 * @property int $status 订单状态
 * @property int $user_id 用户id
 * @property string $consignee 用户姓名
 * @property string $mobile 用户手机号
 * @property int $provider_id 供应商id
 * @property int $restaurant_id 门店id
 * @property string $restaurant_name 门店名称
 * @property float $payment_amount 支付金额
 * @property int $pay_id 支付id
 * @property string $pay_time 支付时间
 * @property string $confirm_time 用户确认收货时间
 * @property string $finish_time 订单关闭时间
 * @property float $refund_amount 退款金额
 * @property string $refund_type 退款方式
 * @property string $refund_remarks 退款备注
 * @property string $refund_time 退款时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder newQuery()
 * @method static \Illuminate\Database\Query\Builder|SetMealOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder whereConfirmTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder whereConsignee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder whereFinishTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder wherePayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder whereRefundAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder whereRefundRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder whereRefundTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder whereRefundType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder whereRestaurantName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealOrder whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|SetMealOrder withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SetMealOrder withoutTrashed()
 * @mixin \Eloquent
 */
class SetMealOrder extends BaseModel
{
    use SetMealOrderStatusTrait;
}
