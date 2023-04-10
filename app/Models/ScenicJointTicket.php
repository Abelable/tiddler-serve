<?php

namespace App\Models;

/**
 * App\Models\ScenicJointTicket
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $shop_id 店铺id
 * @property string $scenic_ids 多个景点id
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
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicJointTicket onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket whereDetailImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket whereMarketPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket wherePromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket whereSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket whereSalesVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket whereScenicIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicJointTicket whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicJointTicket withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicJointTicket withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicJointTicket extends BaseModel
{
}
