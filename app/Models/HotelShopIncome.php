<?php

namespace App\Models;

/**
 * App\Models\HotelShopIncome
 *
 * @property int $id
 * @property int $withdrawal_id 提现记录id
 * @property int $status 收益状态：0-订单待支付，1-待结算, 2-可提现，3-提现中，4-已结算
 * @property int $shop_id 店铺id
 * @property int $order_id 订单id
 * @property string $order_sn 订单编号
 * @property int $room_id 房间id
 * @property float $payment_amount 支付金额
 * @property float $sales_commission_rate 销售佣金比例
 * @property float $income_amount 收入金额
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncome newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncome newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelShopIncome onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncome query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncome whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncome whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncome whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncome whereIncomeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncome whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncome whereOrderSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncome wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncome whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncome whereSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncome whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncome whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncome whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopIncome whereWithdrawalId($value)
 * @method static \Illuminate\Database\Query\Builder|HotelShopIncome withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelShopIncome withoutTrashed()
 * @mixin \Eloquent
 */
class HotelShopIncome extends BaseModel
{
}
