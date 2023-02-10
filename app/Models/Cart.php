<?php

namespace App\Models;

/**
 * App\Models\Cart
 *
 * @property int $id
 * @property int $status 购物车商品状态：1-正常状态，2-所选规格库存为0、所选规格已不存在，3-商品库存为0、商品已下架、商品已删除
 * @property string $status_desc 购物车商品状态描述
 * @property int $user_id 用户id
 * @property int $shop_id 商品所属店铺id
 * @property int $goods_id 商品id
 * @property int $goods_category_id 商品分类id
 * @property string $goods_image 商品图片
 * @property string $goods_name 商品名称
 * @property string $selected_sku_name 选中的规格名称
 * @property int $selected_sku_index 选中的规格索引
 * @property float $price 商品价格
 * @property float $market_price 市场价格
 * @property int $number 商品数量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Cart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart newQuery()
 * @method static \Illuminate\Database\Query\Builder|Cart onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereGoodsCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereGoodsImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereMarketPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereSelectedSkuIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereSelectedSkuName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereStatusDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cart whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Cart withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Cart withoutTrashed()
 * @mixin \Eloquent
 */
class Cart extends BaseModel
{
}
