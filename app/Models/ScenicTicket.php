<?php

namespace App\Models;

/**
 * App\Models\ScenicTicket
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $provider_id 供应商id
 * @property int $category_id 门票分类id
 * @property int $type 门票类型：1-单景点门票，2-多景点联票
 * @property int $status 申请状态：0-待审核，1-审核通过，2-审核未通过，3-下架
 * @property string $failure_reason 审核失败原因
 * @property string $name 门票名称
 * @property float $price 门票最低价格
 * @property float $market_price 门票市场价格
 * @property int $stock 门票总库存
 * @property string $price_list 价格列表：分时间段设置价格及对应库存
 * @property float $sales_commission_rate 销售佣金比例
 * @property float $promotion_commission_rate 推广佣金比例
 * @property int $sales_volume 门票销量
 * @property string $fee_include_tips 费用包含说明
 * @property string $fee_not_include_tips 费用不含说明
 * @property string $booking_time 当天门票最晚预定时间
 * @property string $effective_time 生效时间
 * @property string $validity_time 有效期
 * @property int $limit_number 限购数量
 * @property int $refund_status 退票状态：1-随时可退，2-有条件退，3-不可退
 * @property string $refund_tips 退票说明
 * @property int $need_exchange 是否需要换票：0-无需换票，1-需要换票
 * @property string $exchange_tips 换票说明
 * @property string $exchange_time 换票时间
 * @property string $exchange_location 换票地点
 * @property string $enter_time 入园时间
 * @property string $enter_location 入园地点
 * @property string $other_tips 其他说明
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicTicket onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereBookingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereEffectiveTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereEnterLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereEnterTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereExchangeLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereExchangeTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereExchangeTips($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereFeeIncludeTips($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereFeeNotIncludeTips($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereLimitNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereMarketPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereNeedExchange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereOtherTips($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket wherePriceList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket wherePromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereRefundStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereRefundTips($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereSalesVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereValidityTime($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicTicket withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicTicket withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicTicket extends BaseModel
{
}
