<?php

namespace App\Models\Mall\Goods;

use App\Models\BaseModel;

/**
 * App\Models\ShopIncome
 *
 * @property int $id
 * @property int $status 收益状态：0-订单待支付，1-待结算, 2-可提现，3-提现中，4-已结算
 * @property int|null $withdrawal_id 提现记录id
 * @property int $shop_id 店铺id
 * @property int $order_id 订单id
 * @property string $order_sn 订单编号
 * @property int $goods_id 商品id
 * @property string $total_price 总价
 * @property int $coupon_id 优惠券id
 * @property int $coupon_shop_id 优惠券店铺id
 * @property string $coupon_denomination 优惠券抵扣金额
 * @property string $freight_price 运费
 * @property string $sales_amount 销售额
 * @property string $income_base 收入计算基数
 * @property string $sales_commission_rate 销售佣金比例
 * @property string $income_amount 收入金额
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereCouponDenomination($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereCouponShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereFreightPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereIncomeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereIncomeBase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereSalesAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereWithdrawalId($value)
 * @mixin \Eloquent
 */
class ShopIncome extends BaseModel
{
}
