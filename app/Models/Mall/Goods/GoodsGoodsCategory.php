<?php

namespace App\Models\Mall\Goods;

use App\Models\BaseModel;

/**
 * App\Models\GoodsGoodsCategory
 *
 * @property int $goods_id
 * @property int $goods_category_id
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsGoodsCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsGoodsCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsGoodsCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsGoodsCategory whereGoodsCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsGoodsCategory whereGoodsId($value)
 * @mixin \Eloquent
 */
class GoodsGoodsCategory extends BaseModel
{
    protected static bool $useSoftDeletes = false;
}
