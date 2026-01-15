<?php

namespace App\Models\Activity;

use App\Models\BaseModel;

/**
 * App\Models\Activity\NewYearGoods
 *
 * @property int $id
 * @property int $goods_id 商品id
 * @property string $cover 图片
 * @property string $name 名称
 * @property int $luck_score 兑换所需福气值
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods whereLuckScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearGoods whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class NewYearGoods extends BaseModel
{
}
