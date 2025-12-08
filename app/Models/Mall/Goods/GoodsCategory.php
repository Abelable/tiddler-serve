<?php

namespace App\Models\Mall\Goods;

use App\Models\BaseModel;

/**
 * App\Models\GoodsCategory
 *
 * @property int $id
 * @property string $logo 商品分类图标
 * @property string $name 商品分类名称
 * @property string $description 商品分类描述
 * @property string $min_sales_commission_rate 最小销售佣金比例
 * @property string $max_sales_commission_rate 最大销售佣金比例
 * @property string $min_promotion_commission_rate 最小推广佣金比例
 * @property string $max_promotion_commission_rate 最大推广佣金比例
 * @property string $promotion_commission_upper_limit 推广佣金上限（元）
 * @property string $min_superior_promotion_commission_rate 最小上级推广佣金比例
 * @property string $max_superior_promotion_commission_rate 最大上级推广佣金比例
 * @property string $superior_promotion_commission_upper_limit 上级推广佣金上限（元）
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Mall\Goods\ShopCategory[] $shopCategories
 * @property-read int|null $shop_categories_count
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|GoodsCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereMaxPromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereMaxSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereMaxSuperiorPromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereMinPromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereMinSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereMinSuperiorPromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory wherePromotionCommissionUpperLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereSuperiorPromotionCommissionUpperLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|GoodsCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GoodsCategory withoutTrashed()
 * @mixin \Eloquent
 * @property-read mixed $shop_category_ids
 */
class GoodsCategory extends BaseModel
{
    protected $appends = ['shopCategoryIds'];

    public function getShopCategoryIdsAttribute()
    {
        return $this->shopCategories()->pluck('id')->toArray();
    }

    public function shopCategories()
    {
        return $this->belongsToMany(ShopCategory::class, 'goods_category_shop_categories');
    }
}
