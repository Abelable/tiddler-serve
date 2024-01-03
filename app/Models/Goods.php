<?php

namespace App\Models;

use Laravel\Scout\Searchable;

/**
 * App\Models\Goods
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $shop_id 店铺id
 * @property int $status 申请状态：0-待审核，1-审核通过，2-审核未通过，3-下架
 * @property string $failure_reason 审核失败原因
 * @property int $category_id 商品分类id
 * @property int $shop_category_id 所属店铺分类id
 * @property string $image 列表图片
 * @property string $video 主图视频
 * @property string $image_list 主图图片列表
 * @property string $detail_image_list 详情图片列表
 * @property string $default_spec_image 默认规格图片
 * @property string $name 商品名称
 * @property int $freight_template_id 运费模板id：0-包邮
 * @property int $return_address_id 退货地址id
 * @property float $price 商品价格
 * @property float $market_price 市场价格
 * @property int $stock 商品库存
 * @property float $sales_commission_rate 销售佣金比例
 * @property float $promotion_commission_rate 推广佣金比例
 * @property string $spec_list 商品规格列表
 * @property string $sku_list 商品sku
 * @property int $sales_volume 商品销量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Goods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Goods newQuery()
 * @method static \Illuminate\Database\Query\Builder|Goods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Goods query()
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereDefaultSpecImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereDetailImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereFreightTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereMarketPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods wherePromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereReturnAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereSalesVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereShopCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereSkuList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereSpecList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Goods whereVideo($value)
 * @method static \Illuminate\Database\Query\Builder|Goods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Goods withoutTrashed()
 * @mixin \Eloquent
 */
class Goods extends BaseModel
{
    use Searchable;

    /**
     * 索引的字段
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return $this->only('id', 'name');
    }
}
