<?php

namespace App\Models\Activity;

use App\Models\BaseModel;

/**
 * App\Models\Activity\NewYearPrize
 *
 * @property int $id
 * @property int $type 类型：1-福气值，2-优惠券，3-商品
 * @property int $coupon_id 优惠券id
 * @property int $goods_id 商品id
 * @property int $is_big 是否是大奖：0-否，1-是
 * @property string $cover 奖品图片
 * @property string $name 奖品名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereIsBig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearPrize whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class NewYearPrize extends BaseModel
{
}
