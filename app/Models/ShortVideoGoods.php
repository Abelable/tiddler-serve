<?php

namespace App\Models;

/**
 * App\Models\ShortVideoGoods
 *
 * @property int $id
 * @property int $video_id 视频id
 * @property int $goods_id 商品id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShortVideoGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoGoods whereVideoId($value)
 * @method static \Illuminate\Database\Query\Builder|ShortVideoGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShortVideoGoods withoutTrashed()
 * @mixin \Eloquent
 */
class ShortVideoGoods extends BaseModel
{
}
