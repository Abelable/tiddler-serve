<?php

namespace App\Models\Mall\Goods;

use App\Models\BaseModel;
use Laravel\Scout\Searchable;

/**
 * App\Models\Goods
 *
 * @property int $id
 * @property int $status 申请状态：0-待审核，1-审核通过，2-审核未通过，3-下架
 * @property string|null $failure_reason 审核失败原因
 * @property int $shop_id 店铺id
 * @property string $cover 列表图片
 * @property string|null $video 主图视频
 * @property string $image_list 主图图片列表
 * @property string $detail_image_list 详情图片列表
 * @property string $default_spec_image 默认规格图片
 * @property string $name 商品名称
 * @property string|null $introduction 商品介绍
 * @property int $freight_template_id 运费模板id：0-包邮
 * @property string $price 商品价格
 * @property string $market_price 市场价格
 * @property string $sales_commission_rate 销售佣金比例%（如15.5表示15.5%）
 * @property string $promotion_commission_rate 推广佣金比例%（如10.5表示10.5%）
 * @property string $promotion_commission_upper_limit 推广佣金上限
 * @property string $superior_promotion_commission_rate 上级推广佣金比例%
 * @property string $superior_promotion_commission_upper_limit 上级推广佣金上限
 * @property int $stock 商品库存
 * @property int $number_limit 限购数量
 * @property string $spec_list 商品规格列表
 * @property string $sku_list 商品sku
 * @property int $delivery_mode 提货方式：1-快递，2-自提，3-快递/自提
 * @property int $refund_status 是否支持7天无理由：0-不支持，1-支持
 * @property int $refund_address_id 退货地址id
 * @property int $sales_volume 销量
 * @property string $score 评分
 * @property int $views 点击率
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Mall\Goods\GoodsCategory[] $categories
 * @property-read int|null $categories_count
 * @property-read \App\Models\Mall\Goods\FreightTemplate|null $freightTemplateInfo
 * @property-read mixed $category_ids
 * @property-read mixed $shop_category_ids
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Mall\Goods\GoodsPickupAddress[] $pickupAddressList
 * @property-read int|null $pickup_address_list_count
 * @property-read \App\Models\Mall\Goods\Shop|null $shopInfo
 * @method static \Illuminate\Database\Eloquent\Builder|Goods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Goods newQuery()
 * @method static \Illuminate\Database\Query\Builder|Goods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Goods query()
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereDefaultSpecImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereDeliveryMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereDetailImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereFreightTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereIntroduction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereMarketPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereNumberLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods wherePromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods wherePromotionCommissionUpperLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereRefundAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereRefundStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereSalesVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereSkuList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereSpecList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereSuperiorPromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereSuperiorPromotionCommissionUpperLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereVideo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereViews($value)
 * @method static \Illuminate\Database\Query\Builder|Goods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Goods withoutTrashed()
 * @mixin \Eloquent
 */
class Goods extends BaseModel
{
    use Searchable;

    protected $appends = ['shopCategoryIds', 'categoryIds'];

    /**
     * 索引的字段
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return $this->only('id', 'name');
    }

    public function getShopCategoryIdsAttribute()
    {
        return $this->categories->flatMap(function ($category) {
            return $category->shopCategories->pluck('id');
        })->unique()->values()->toArray();
    }

    public function getCategoryIdsAttribute()
    {
        return $this->categories()->pluck('id')->toArray();
    }

    public function categories()
    {
        return $this->belongsToMany(GoodsCategory::class, 'goods_goods_categories');
    }

    public function freightTemplateInfo()
    {
        return $this->belongsTo(FreightTemplate::class, 'freight_template_id');
    }

    public function pickupAddressList()
    {
        return $this->hasMany(GoodsPickupAddress::class, 'goods_id');
    }

    public function pickupAddressIds()
    {
        return $this->pickupAddressList()->pluck('pickup_address_id');
    }

    public function shopInfo()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}
