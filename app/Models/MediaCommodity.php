<?php

namespace App\Models;

/**
 * App\Models\MediaCommodity
 *
 * @property int $id
 * @property int $media_type 媒体类型：1-短视频，2-图文游记
 * @property int $media_id 媒体id
 * @property int $commodity_type 商品类型：1-景点，2-酒店，3-餐馆，4-商品
 * @property int $commodity_id 商品id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity newQuery()
 * @method static \Illuminate\Database\Query\Builder|MediaCommodity onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity query()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity whereCommodityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity whereCommodityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity whereMediaType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|MediaCommodity withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MediaCommodity withoutTrashed()
 * @mixin \Eloquent
 */
class MediaCommodity extends BaseModel
{
}
