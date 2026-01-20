<?php

namespace App\Models\Activity;

use App\Models\BaseModel;

/**
 * App\Models\Activity\NewYearDrawLog
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int|null $prize_id 奖品id，未中奖可为空
 * @property int $prize_type 奖品类型：0-谢谢参与，1-福气值，2-优惠券，3-商品
 * @property string $prize_cover 奖品图片
 * @property string $prize_name 奖品名称
 * @property int $prize_cost 对应奖品成本/福气值/优惠券面值，未中奖为0
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearDrawLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearDrawLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearDrawLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearDrawLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearDrawLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearDrawLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearDrawLog wherePrizeCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearDrawLog wherePrizeCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearDrawLog wherePrizeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearDrawLog wherePrizeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearDrawLog wherePrizeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearDrawLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearDrawLog whereUserId($value)
 * @mixin \Eloquent
 */
class NewYearDrawLog extends BaseModel
{
}
