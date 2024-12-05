<?php

namespace App\Models;

/**
 * App\Models\TeamCommission
 *
 * @property int $id
 * @property int $status 佣金状态：0-订单待支付，1-待结算, 2-可提现，3-已结算
 * @property int $manager_id 组织者id
 * @property int $manager_level 组织者等级
 * @property int $user_id 用户id
 * @property int $order_id 订单id
 * @property int $commodity_id 商品id
 * @property int $commodity_type 商品类型：1-景点，2-酒店，3-餐馆，4-商品
 * @property float $total_price 商品总价
 * @property float $coupon_denomination 优惠券抵扣
 * @property float $commission_base 商品佣金计算基数
 * @property float $commission_rate 商品佣金比例%
 * @property float $commission_amount 佣金金额
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission newQuery()
 * @method static \Illuminate\Database\Query\Builder|TeamCommission onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission query()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereCommissionAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereCommissionBase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereCommodityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereCommodityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereCouponDenomination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereManagerLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamCommission whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|TeamCommission withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TeamCommission withoutTrashed()
 * @mixin \Eloquent
 */
class TeamCommission extends BaseModel
{
}
