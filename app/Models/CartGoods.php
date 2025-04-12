<?php

namespace App\Models;

/**
 * App\Models\CartGoods
 *
 * @property int $id
 * @property int $scene 场景值：1-添加购物车，2-直接购买
 * @property int $status 购物车商品状态：1-正常状态，2-所选规格库存为0、所选规格已不存在，3-商品库存为0、商品已下架、商品已删除
 * @property string $status_desc 购物车商品状态描述
 * @property int $user_id 用户id
 * @property int $goods_id 商品id
 * @property int $shop_id 商品所属店铺id
 * @property int $shop_category_id 商品店铺分类id
 * @property int $freight_template_id 运费模板id
 * @property int $is_gift 是否为礼包商品：0-否，1-是
 * @property int $refund_status 是否支持7天无理由：0-不支持，1-支持
 * @property int $delivery_mode 提货方式：1-快递，2-自提，3-快递/自提
 * @property string $cover 商品图片
 * @property string $name 商品名称
 * @property string $selected_sku_name 选中的规格名称
 * @property int $selected_sku_index 选中的规格索引
 * @property float $price 商品价格
 * @property float $market_price 市场价格
 * @property float $sales_commission_rate 销售佣金比例%
 * @property float $promotion_commission_rate 推广佣金比例%
 * @property float $promotion_commission_upper_limit 推广佣金上限
 * @property float $superior_promotion_commission_rate 上级推广佣金比例%
 * @property float $superior_promotion_commission_upper_limit 上级推广佣金上限
 * @property int $number 商品数量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods newQuery()
 * @method static \Illuminate\Database\Query\Builder|CartGoods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereDeliveryMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereFreightTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereIsGift($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereMarketPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods wherePromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods wherePromotionCommissionUpperLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereRefundStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereScene($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereSelectedSkuIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereSelectedSkuName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereShopCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereStatusDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereSuperiorPromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereSuperiorPromotionCommissionUpperLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartGoods whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|CartGoods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CartGoods withoutTrashed()
 * @mixin \Eloquent
 */
class CartGoods extends BaseModel
{
}
