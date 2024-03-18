<?php

namespace App\Models;

use App\Utils\Traits\ScenicOrderStatusTrait;

/**
 * App\Models\ScenicOrder
 *
 * @property int $id
 * @property string $order_sn 订单编号
 * @property int $status 订单状态
 * @property int $user_id 用户id
 * @property string $consignee 出游人姓名
 * @property string $mobile 出游人手机号
 * @property string $id_card_number 出游人身份证号
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
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicOrder onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder whereConfirmTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder whereConsignee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder whereFinishTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder whereIdCardNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder wherePayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder wherePayTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder whereRefundAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder whereRefundRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder whereRefundTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder whereRefundType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder whereShopAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder whereShopName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrder whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicOrder withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicOrder withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicOrder extends BaseModel
{
    use ScenicOrderStatusTrait;
}
