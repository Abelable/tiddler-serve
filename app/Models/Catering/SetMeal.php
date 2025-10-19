<?php

namespace App\Models\Catering;

use App\Models\BaseModel;

/**
 * App\Models\Catering\SetMeal
 *
 * @property int $id
 * @property int $status 申请状态：0-待审核，1-审核通过，2-审核未通过，3-下架
 * @property string $failure_reason 审核失败原因
 * @property int $shop_id 店铺id
 * @property string $cover 套餐图片
 * @property string $name 套餐名称
 * @property float $price 套餐价格
 * @property float $original_price 套餐原价
 * @property float $sales_commission_rate 销售佣金比例%
 * @property float $promotion_commission_rate 推广佣金比例%
 * @property float $promotion_commission_upper_limit 推广佣金上限
 * @property float $superior_promotion_commission_rate 上级推广佣金比例%
 * @property float $superior_promotion_commission_upper_limit 上级推广佣金上限
 * @property int $sales_volume 销量
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
 * @property-read \App\Models\Catering\CateringShop|null $shopInfo
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal newQuery()
 * @method static \Illuminate\Database\Query\Builder|SetMeal onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal query()
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereBuyLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereNeedPreBook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereOriginalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal wherePackageDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal wherePerTableUsageLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal wherePromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal wherePromotionCommissionUpperLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereSalesVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereSuperiorPromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereSuperiorPromotionCommissionUpperLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereUseRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereUseTimeList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereValidityDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereValidityEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMeal whereValidityStartTime($value)
 * @method static \Illuminate\Database\Query\Builder|SetMeal withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SetMeal withoutTrashed()
 * @mixin \Eloquent
 */
class SetMeal extends BaseModel
{
    public function restaurantIds(): array
    {
        return $this
            ->hasMany(SetMealRestaurant::class, 'set_meal_id')
            ->pluck('restaurant_id')
            ->toArray();
    }

    public function shopInfo()
    {
        return $this->belongsTo(CateringShop::class, 'shop_id');
    }
}
