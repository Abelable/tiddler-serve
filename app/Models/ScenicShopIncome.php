<?php

namespace App\Models;

/**
 * App\Models\ScenicShopIncome
 *
 * @property int $id
 * @property int $withdrawal_id 提现记录id
 * @property int $status 收益状态：0-订单待支付，1-待结算, 2-可提现，3-提现中，4-已结算
 * @property int $shop_id 店铺id
 * @property int $order_id 订单id
 * @property string $order_sn 订单编号
 * @property int $ticket_id 门票id
 * @property float $payment_amount 支付金额
 * @property float $sales_commission_rate 销售佣金比例
 * @property float $income_amount 收入金额
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncome newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncome newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicShopIncome onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncome query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncome whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncome whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncome whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncome whereIncomeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncome whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncome whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncome wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncome whereSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncome whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncome whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncome whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncome whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopIncome whereWithdrawalId($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicShopIncome withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicShopIncome withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicShopIncome extends BaseModel
{
}
