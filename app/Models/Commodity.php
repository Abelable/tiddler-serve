<?php

namespace App\Models;

/**
 * App\Models\Commodity
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $shop_id 店铺id
 * @property int $status 申请状态：0-待审核，1-审核通过，2-下架
 * @property string $failure_reason 审核失败原因
 * @property string $video 主图视频
 * @property string $image_list 主图图片
 * @property string $name 商品名称
 * @property int $freight_template_id 运费模板id：0-包邮
 * @property int $category_id 商品分类id
 * @property int $return_address_id 退货地址id
 * @property float $price 商品价格
 * @property float $market_price 市场价格
 * @property int $stock 商品库存
 * @property float $commission_rate 推广佣金比例
 * @property string $detail_image_list 商品详情图片
 * @property string $spec_list 商品规格列表，使用场景：编辑商品信息
 * @property string $sku_list 商品sku，使用场景：购买商品
 * @property int $sales_volume 商品销量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity newQuery()
 * @method static \Illuminate\Database\Query\Builder|Commodity onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity query()
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereDetailImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereFreightTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereMarketPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereReturnAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereSalesVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereSkuList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereSpecList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commodity whereVideo($value)
 * @method static \Illuminate\Database\Query\Builder|Commodity withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Commodity withoutTrashed()
 * @mixin \Eloquent
 */
class Commodity extends BaseModel
{

}
