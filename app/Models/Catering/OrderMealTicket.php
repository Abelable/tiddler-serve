<?php

namespace App\Models\Catering;

use App\Models\BaseModel;

/**
 * App\Models\Catering\OrderMealTicket
 *
 * @property int $id
 * @property int $order_id 订单id
 * @property int $number 代金券数量
 * @property int $ticket_id 代金券id
 * @property float $price 代金券售价
 * @property float $original_price 代金券抵扣价格
 * @property int $validity_days 有效天数
 * @property string $validity_start_time 范围有效期开始时间
 * @property string $validity_end_time 范围有效期结束时间
 * @property int $buy_limit 限购数量
 * @property int $per_table_usage_limit 单桌使用数量限制
 * @property int $overlay_usage_limit 叠加使用数量限制
 * @property string $use_time_list 使用时间范围
 * @property string $inapplicable_products 不可用商品列表
 * @property int $box_available 包厢是否可用：0-不可用，1-可用
 * @property int $need_pre_book 是否需要预定：0-不需要预定，1-需要预定
 * @property string $use_rules 使用规则
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrderMealTicket onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereBoxAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereBuyLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereInapplicableProducts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereNeedPreBook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereOriginalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereOverlayUsageLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket wherePerTableUsageLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereUseRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereUseTimeList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereValidityDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereValidityEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderMealTicket whereValidityStartTime($value)
 * @method static \Illuminate\Database\Query\Builder|OrderMealTicket withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrderMealTicket withoutTrashed()
 * @mixin \Eloquent
 */
class OrderMealTicket extends BaseModel
{
}
