<?php

namespace App\Models\Catering;

use App\Models\BaseModel;

/**
 * App\Models\Catering\OrderSetMeal
 *
 * @property int $id
 * @property int $order_id 订单id
 * @property int $number 套餐数量
 * @property int $set_meal_id 套餐id
 * @property string $cover 套餐图片
 * @property string $name 套餐名称
 * @property float $price 套餐售价
 * @property float $original_price 套餐抵扣价格
 * @property string $package_details 套餐详情
 * @property int $validity_days 有效天数
 * @property string $validity_start_time 范围有效期开始时间
 * @property string $validity_end_time 范围有效期结束时间
 * @property int $buy_limit 限购数量
 * @property int $per_table_usage_limit 单桌使用数量限制
 * @property string $use_time_list 使用时间范围
 * @property int $need_pre_book 是否需要预定：0-不需要预定，1-需要预定
 * @property string $use_rules 使用规则
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrderSetMeal onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal whereBuyLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal whereNeedPreBook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal whereOriginalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal wherePackageDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal wherePerTableUsageLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal whereSetMealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal whereUseRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal whereUseTimeList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal whereValidityDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal whereValidityEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderSetMeal whereValidityStartTime($value)
 * @method static \Illuminate\Database\Query\Builder|OrderSetMeal withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrderSetMeal withoutTrashed()
 * @mixin \Eloquent
 */
class OrderSetMeal extends BaseModel
{
}
