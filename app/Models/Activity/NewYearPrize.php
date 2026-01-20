<?php

namespace App\Models\Activity;

use App\Models\BaseModel;

/**
 * App\Models\Activity\NewYearPrize
 *
 * @property int $id
 * @property int $status 状态：1-上架中；2-已下架
 * @property int $type 类型：1-福气值，2-优惠券，3-商品
 * @property int $coupon_id 优惠券id
 * @property int $goods_id 商品id
 * @property int $is_big 是否是大奖：0-否，1-是
 * @property string $cover 奖品图片
 * @property string $name 奖品名称
 * @property int $sort 排序值
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $rate 抽奖概率，0~1
 * @property int $stock 库存：-1不限，0售罄
 * @property int $luck_score 福气值数量，仅 type=1 有效
 * @property string $cost 单次命中真实成本
 * @property int $limit_per_user 单用户最多命中次数，0不限
 * @property string|null $start_at 生效开始时间
 * @property string|null $end_at 生效结束时间
 * @property int $fallback_prize_id 库存不足降级奖品
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereFallbackPrizeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereIsBig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereLimitPerUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereLuckScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class NewYearPrize extends BaseModel
{
}
