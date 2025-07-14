<?php

namespace App\Models;

/**
 * App\Models\HotelOrderRoom
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $order_id 订单id
 * @property int $hotel_id 酒店id
 * @property string $hotel_name 酒店名称
 * @property int $room_id 房间id
 * @property int $type_id 房间类型id
 * @property string $type_name 房间类型名称
 * @property string $check_in_date 入住时间
 * @property string $check_out_date 退房时间
 * @property float $price 房间价格
 * @property float $sales_commission_rate 销售佣金比例
 * @property float $promotion_commission_rate 推广佣金比例%
 * @property float $promotion_commission_upper_limit 推广佣金上限
 * @property float $superior_promotion_commission_rate 上级推广佣金比例%
 * @property float $superior_promotion_commission_upper_limit 上级推广佣金上限
 * @property int $number 房间数量
 * @property int $breakfast_num 早餐数量
 * @property int $guest_num 入住人数
 * @property int $cancellable 免费取消：0-不可取消，1-可免费取消
 * @property string $image_list 房间照片
 * @property string $bed_desc 床铺描述
 * @property float $area_size 房间面积
 * @property string $floor_desc 楼层描述
 * @property string $facility_list 房间设施
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelOrderRoom onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereAreaSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereBedDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereBreakfastNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereCancellable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereCheckInDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereCheckOutDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereFacilityList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereFloorDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereGuestNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereHotelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom wherePromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom wherePromotionCommissionUpperLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereSuperiorPromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereSuperiorPromotionCommissionUpperLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereTypeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderRoom whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|HotelOrderRoom withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelOrderRoom withoutTrashed()
 * @mixin \Eloquent
 */
class HotelOrderRoom extends BaseModel
{
}
