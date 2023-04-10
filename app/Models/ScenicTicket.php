<?php

namespace App\Models;

/**
 * App\Models\ScenicTicket
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $shop_id 店铺id
 * @property int $scenic_id 景点id
 * @property int $status 申请状态：0-待审核，1-审核通过，2-审核未通过，3-下架
 * @property string $failure_reason 审核失败原因
 * @property string $image 列表图片
 * @property string $detail_image_list 详情图片列表
 * @property string $name 商品名称
 * @property float $price 商品价格
 * @property float $market_price 市场价格
 * @property int $stock 商品库存
 * @property float $sales_commission_rate 销售佣金比例
 * @property float $promotion_commission_rate 推广佣金比例
 * @property int $sales_volume 商品销量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicTicket onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereDetailImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereMarketPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket wherePromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereSalesVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereScenicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicTicket whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicTicket withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicTicket withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicTicket extends BaseModel
{
}
