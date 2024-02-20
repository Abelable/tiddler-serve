<?php

namespace App\Models;

/**
 * App\Models\MallBanner
 *
 * @property int $id
 * @property string $cover 活动封面
 * @property string $desc 活动描述
 * @property int $scene 链接跳转场景值：1-h5活动，2-景点详情，3-酒店详情，4-餐饮门店详情， 5-商品详情
 * @property string $value 链接参数值
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MallBanner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MallBanner newQuery()
 * @method static \Illuminate\Database\Query\Builder|MallBanner onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MallBanner query()
 * @method static \Illuminate\Database\Eloquent\Builder|MallBanner whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MallBanner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MallBanner whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MallBanner whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MallBanner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MallBanner whereScene($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MallBanner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MallBanner whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|MallBanner withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MallBanner withoutTrashed()
 * @mixin \Eloquent
 */
class MallBanner extends BaseModel
{
}
