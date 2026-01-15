<?php

namespace App\Models\Activity;

use App\Models\BaseModel;

/**
 * App\Models\Activity\NewYearUserPrize
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $prize_id 奖品id
 * @property int $prize_type 奖品类型：1-福气值，2-优惠券，3-商品
 * @property int $status 奖品状态：0-未使用，1-已使用
 * @property string $cover 奖品图片
 * @property string $name 奖品名称
 * @property int $coupon_id 优惠券id
 * @property int $goods_id 商品id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize wherePrizeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize wherePrizeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereUserId($value)
 * @mixin \Eloquent
 */
class NewYearUserPrize extends BaseModel
{
}
