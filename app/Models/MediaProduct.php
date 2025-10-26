<?php

namespace App\Models;

/**
 * App\Models\MediaProduct
 *
 * @property int $id
 * @property int $media_type 媒体类型：1-视频游记，2-图文游记
 * @property int $media_id 媒体id
 * @property int $product_type 商品类型：1-景点，2-酒店，3-餐馆，4-商品
 * @property int $product_id 商品id
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MediaProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaProduct newQuery()
 * @method static \Illuminate\Database\Query\Builder|MediaProduct onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaProduct whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaProduct whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaProduct whereMediaType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaProduct whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaProduct whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaProduct whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|MediaProduct withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MediaProduct withoutTrashed()
 * @mixin \Eloquent
 */
class MediaProduct extends BaseModel
{
}
