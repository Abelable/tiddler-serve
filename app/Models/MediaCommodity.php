<?php

namespace App\Models;

/**
 * App\Models\MediaCommodity
 *
 * @property int $id
 * @property int $media_type 媒体类型：1-短视频，2-图文游记
 * @property int $media_id 媒体id
 * @property int $scenic_id 媒体关联酒店id
 * @property int $hotel_id 媒体关联酒店id
 * @property int $restaurant_id 媒体关联餐馆id
 * @property int $goods_id 媒体关联商品id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity newQuery()
 * @method static \Illuminate\Database\Query\Builder|MediaCommodity onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity query()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity whereMediaType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity whereScenicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaCommodity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|MediaCommodity withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MediaCommodity withoutTrashed()
 * @mixin \Eloquent
 */
class MediaCommodity extends BaseModel
{
}
