<?php

namespace App\Models\Catering;

use App\Models\BaseModel;

/**
 * App\Models\Catering\MealTicket
 *
 * @property int $id
 * @property int $status 申请状态：0-待审核，1-审核通过，2-审核未通过，3-下架
 * @property string $failure_reason 审核失败原因
 * @property int $shop_id 店铺id
 * @property float $price 代金券价格
 * @property float $original_price 抵扣原价
 * @property float $sales_commission_rate 销售佣金比例%
 * @property float $promotion_commission_rate 推广佣金比例%
 * @property float $promotion_commission_upper_limit 推广佣金上限
 * @property float $superior_promotion_commission_rate 上级推广佣金比例%
 * @property float $superior_promotion_commission_upper_limit 上级推广佣金上限
 * @property int $sales_volume 代金券销量
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
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket newQuery()
 * @method static \Illuminate\Database\Query\Builder|MealTicket onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereBoxAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereBuyLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereInapplicableProducts($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereNeedPreBook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereOriginalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereOverlayUsageLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket wherePerTableUsageLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket wherePromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket wherePromotionCommissionUpperLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereSalesVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereSuperiorPromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereSuperiorPromotionCommissionUpperLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereUseRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereUseTimeList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereValidityDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereValidityEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereValidityStartTime($value)
 * @method static \Illuminate\Database\Query\Builder|MealTicket withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MealTicket withoutTrashed()
 * @mixin \Eloquent
 */
class MealTicket extends BaseModel
{
    public function restaurantIds(): array
    {
        return $this
            ->hasMany(MealTicketRestaurant::class, 'ticket_id')
            ->pluck('restaurant_id')
            ->toArray();
    }
}
