<?php

namespace App\Models;

/**
 * App\Models\Commission
 *
 * @property int $id
 * @property int $withdrawal_id 提现记录id
 * @property int $status 佣金状态：0-订单待支付，1-待结算, 2-可提现，3-提现中，4-已结算
 * @property int $scene 场景：1-自购 2-直推分享 3-间推分享 4-直推团队 5-间推团队
 * @property int $promoter_id 代言人id
 * @property int $promoter_level 代言人等级
 * @property int $user_id 用户id
 * @property int $order_id 订单id
 * @property string $order_sn 订单编号
 * @property int $product_type 产品类型：1-景点，2-酒店，4-商品，5-套餐，6-餐券
 * @property int $product_id 产品id
 * @property int $refund_status 是否支持7天无理由：0-不支持，1-支持
 * @property float $payment_amount 支付金额
 * @property float $commission_base 佣金基数
 * @property float $commission_rate 佣金系数
 * @property float $commission_limit 佣金上限
 * @property float $commission_amount 佣金金额
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Commission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Commission newQuery()
 * @method static \Illuminate\Database\Query\Builder|Commission onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Commission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereCommissionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereCommissionBase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereCommissionLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission wherePromoterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission wherePromoterLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereRefundStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereScene($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereWithdrawalId($value)
 * @method static \Illuminate\Database\Query\Builder|Commission withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Commission withoutTrashed()
 * @mixin \Eloquent
 */
class Commission extends BaseModel
{
}
