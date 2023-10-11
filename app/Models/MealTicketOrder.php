<?php

namespace App\Models;

/**
 * App\Models\MealTicketOrder
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
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder newQuery()
 * @method static \Illuminate\Database\Query\Builder|MealTicketOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder whereConfirmTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder whereConsignee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder whereFinishTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder wherePayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder whereRefundAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder whereRefundRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder whereRefundTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder whereRefundType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder whereRestaurantName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketOrder whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|MealTicketOrder withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MealTicketOrder withoutTrashed()
 * @mixin \Eloquent
 */
class MealTicketOrder extends BaseModel
{
}
