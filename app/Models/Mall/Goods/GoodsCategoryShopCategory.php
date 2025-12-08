<?php

namespace App\Models\Mall\Goods;

use App\Models\BaseModel;

/**
 * App\Models\GoodsCategoryShopCategory
 *
 * @property int $goods_category_id
 * @property int $shop_category_id
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategoryShopCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategoryShopCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategoryShopCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategoryShopCategory whereGoodsCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategoryShopCategory whereShopCategoryId($value)
 * @mixin \Eloquent
 */
class GoodsCategoryShopCategory extends BaseModel
{
    protected static bool $useSoftDeletes = false;
}
