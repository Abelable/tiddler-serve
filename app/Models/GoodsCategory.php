<?php

namespace App\Models;

/**
 * App\Models\GoodsCategory
 *
 * @property int $id
 * @property int $shop_category_id 店铺分类id
 * @property string $name 商品分类名称
 * @property int $min_sales_commission_rate 最小销售佣金比例
 * @property int $max_sales_commission_rate 最大销售佣金比例
 * @property int $min_promotion_commission_rate 最小推广佣金比例
 * @property int $max_promotion_commission_rate 最大推广佣金比例
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|GoodsCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereMaxPromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereMaxSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereMinPromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereMinSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereShopCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|GoodsCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GoodsCategory withoutTrashed()
 * @mixin \Eloquent
 */
class GoodsCategory extends BaseModel
{
}
