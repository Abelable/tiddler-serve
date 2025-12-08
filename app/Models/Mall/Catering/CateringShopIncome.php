<?php

namespace App\Models\Mall\Catering;

use App\Models\BaseModel;

/**
 * App\Models\Catering\CateringShopIncome
 *
 * @property int $id
 * @property int $withdrawal_id 提现记录id
 * @property int $status 收益状态：0-订单待支付，1-待结算, 2-可提现，3-提现中，4-已结算
 * @property int $shop_id 店铺id
 * @property int $order_id 订单id
 * @property string $order_sn 订单编号
 * @property int $product_type 商品类型：1-餐券，2-套餐
 * @property int $product_id 商品id
 * @property float $payment_amount 支付金额
 * @property float $sales_commission_rate 销售佣金比例
 * @property float $income_amount 收入金额
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopIncome newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopIncome newQuery()
 * @method static \Illuminate\Database\Query\Builder|CateringShopIncome onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopIncome query()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopIncome whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopIncome whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopIncome whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopIncome whereIncomeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopIncome whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopIncome whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopIncome wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopIncome whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopIncome whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopIncome whereSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopIncome whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopIncome whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopIncome whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopIncome whereWithdrawalId($value)
 * @method static \Illuminate\Database\Query\Builder|CateringShopIncome withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CateringShopIncome withoutTrashed()
 * @mixin \Eloquent
 */
class CateringShopIncome extends BaseModel
{
}
