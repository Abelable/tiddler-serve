<?php

namespace App\Models;

/**
 * App\Models\StarTrip
 *
 * @property int $id
 * @property int $product_type 产品类型
 * @property int $product_id 产品id
 * @property string $cover 封面
 * @property string $name 名称
 * @property string $desc 描述
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|StarTrip newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StarTrip newQuery()
 * @method static \Illuminate\Database\Query\Builder|StarTrip onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StarTrip query()
 * @method static \Illuminate\Database\Eloquent\Builder|StarTrip whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StarTrip whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StarTrip whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StarTrip whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StarTrip whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StarTrip whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StarTrip whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StarTrip whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StarTrip whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StarTrip whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|StarTrip withTrashed()
 * @method static \Illuminate\Database\Query\Builder|StarTrip withoutTrashed()
 * @mixin \Eloquent
 */
class StarTrip extends BaseModel
{
}
