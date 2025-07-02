<?php

namespace App\Models;

/**
 * App\Models\ShopIncome
 *
 * @property int $id
 * @property int $withdrawal_id 提现记录id
 * @property int $status 收益状态：0-订单待支付，1-待结算, 2-可提现，3-提现中，4-已结算
 * @property int $shop_id 店铺id
 * @property int $order_id 订单id
 * @property string $order_sn 订单编号
 * @property int $goods_id 商品id
 * @property int $refund_status 是否支持7天无理由：0-不支持，1-支持
 * @property float $payment_amount 支付金额
 * @property float $sales_commission_rate 销售佣金比例
 * @property float $income_amount 收入金额
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShopIncome onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereIncomeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereRefundStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopIncome whereWithdrawalId($value)
 * @method static \Illuminate\Database\Query\Builder|ShopIncome withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShopIncome withoutTrashed()
 * @mixin \Eloquent
 */
class ShopIncome extends BaseModel
{
}
