<?php

namespace App\Models;

/**
 * App\Models\MealTicket
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $provider_id 供应商id
 * @property int $status 申请状态：0-待审核，1-审核通过，2-审核未通过，3-下架
 * @property string $failure_reason 审核失败原因
 * @property string $name 优惠券名称
 * @property float $price 优惠券价格
 * @property float $original_price 抵扣原价
 * @property float $sales_commission_rate 销售佣金比例
 * @property float $promotion_commission_rate 推广佣金比例
 * @property int $sales_volume 优惠券销量
 * @property int $validity_days 有效天数
 * @property string $validity_start_time 范围有效期开始时间
 * @property string $validity_end_time 范围有效期结束时间
 * @property int $buy_limit_number 限购数量
 * @property int $use_limit_number 使用数量限制
 * @property string $use_time_list 使用时间范围
 * @property int $including_drink 全场通用是否包含酒水：0-不含酒水，1-包含酒水
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
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereBuyLimitNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereIncludingDrink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereNeedPreBook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereOriginalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket wherePromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereSalesVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereUseLimitNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereUseRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereUseTimeList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereValidityDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereValidityEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicket whereValidityStartTime($value)
 * @method static \Illuminate\Database\Query\Builder|MealTicket withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MealTicket withoutTrashed()
 * @mixin \Eloquent
 */
class MealTicket extends BaseModel
{
}
