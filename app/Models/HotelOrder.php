<?php

namespace App\Models;

/**
 * App\Models\HotelOrder
 *
 * @property int $id
 * @property string $order_sn 订单编号
 * @property int $status 订单状态
 * @property int $user_id 用户id
 * @property string $consignee 出游人姓名
 * @property string $mobile 出游人手机号
 * @property int $shop_id 店铺id
 * @property string $shop_avatar 店铺头像
 * @property string $shop_name 店铺名称
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
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereConfirmTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereConsignee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereFinishTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder wherePayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereRefundAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereRefundRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereRefundTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereRefundType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereShopAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereShopName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|HotelOrder withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelOrder withoutTrashed()
 * @mixin \Eloquent
 */
class HotelOrder extends BaseModel
{
}
