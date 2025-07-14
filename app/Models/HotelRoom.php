<?php

namespace App\Models;

/**
 * App\Models\HotelRoom
 *
 * @property int $id
 * @property int $status 申请状态：0-待审核，1-审核通过，2-审核未通过，3-下架
 * @property string $failure_reason 审核失败原因
 * @property int $shop_id 店铺id
 * @property int $hotel_id 酒店id
 * @property int $type_id 房间类型id
 * @property float $price 房间起始价格
 * @property float $sales_commission_rate 销售佣金比例
 * @property float $promotion_commission_rate 推广佣金比例
 * @property float $promotion_commission_upper_limit 推广佣金上限
 * @property float $superior_promotion_commission_rate 上级推广佣金比例%
 * @property float $superior_promotion_commission_upper_limit 上级推广佣金上限
 * @property int $sales_volume 房间销量
 * @property string $price_list 价格列表：分时间段设置价格
 * @property int $breakfast_num 早餐份数
 * @property int $guest_num 可入住客人数量
 * @property int $cancellable 免费取消：0-不可取消，1-可免费取消
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelRoom onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom whereBreakfastNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom whereCancellable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom whereGuestNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom wherePriceList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom wherePromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom wherePromotionCommissionUpperLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom whereSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom whereSalesVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom whereSuperiorPromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom whereSuperiorPromotionCommissionUpperLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoom whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|HotelRoom withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelRoom withoutTrashed()
 * @mixin \Eloquent
 */
class HotelRoom extends BaseModel
{
}
