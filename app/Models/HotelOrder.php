<?php

namespace App\Models;

use App\Utils\Traits\HotelOrderStatusTrait;

/**
 * App\Models\HotelOrder
 *
 * @property int $id
 * @property string $order_sn 订单编号
 * @property int $status 订单状态
 * @property int $user_id 用户id
 * @property int $shop_id 店铺id
 * @property string $shop_logo 店铺头像
 * @property string $shop_name 店铺名称
 * @property int $hotel_id 酒店id
 * @property string $hotel_cover 酒店头像
 * @property string $hotel_name 酒店名称
 * @property string $consignee 入住人姓名
 * @property string $mobile 入住人手机号
 * @property float $total_price 房间总价
 * @property int $coupon_id 优惠券id
 * @property float $coupon_denomination 优惠券抵扣金额
 * @property float $deduction_balance 余额抵扣金额
 * @property float $payment_amount 支付金额
 * @property string $pay_id 支付id
 * @property string $pay_time 支付时间
 * @property string $approve_time 商家确认时间
 * @property string $confirm_time 用户核销使用时间
 * @property string $finish_time 订单关闭时间
 * @property float $refund_amount 退款金额
 * @property string $refund_id 微信退款id
 * @property string $refund_type 退款方式
 * @property string $refund_remarks 退款备注
 * @property string $refund_time 退款时间
 * @property string $remarks 订单备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereApproveTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereConfirmTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereConsignee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereCouponDenomination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereDeductionBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereFinishTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereHotelCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereHotelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder wherePayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereRefundAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereRefundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereRefundRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereRefundTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereRefundType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereShopLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereShopName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrder whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|HotelOrder withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelOrder withoutTrashed()
 * @mixin \Eloquent
 */
class HotelOrder extends BaseModel
{
    use HotelOrderStatusTrait;
}
