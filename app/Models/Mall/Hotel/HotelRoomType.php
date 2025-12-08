<?php

namespace App\Models\Mall\Hotel;

use App\Models\BaseModel;

/**
 * App\Models\HotelRoomType
 *
 * @property int $id
 * @property int $hotel_id 酒店id
 * @property string $name 酒店房型名称
 * @property string $image_list 房间照片
 * @property string $bed_desc 床铺描述
 * @property float $area_size 房间面积
 * @property string $floor_desc 楼层描述
 * @property string $facility_list 房间设施
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoomType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoomType newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelRoomType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoomType query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoomType whereAreaSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoomType whereBedDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoomType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoomType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoomType whereFacilityList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoomType whereFloorDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoomType whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoomType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoomType whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoomType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelRoomType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|HotelRoomType withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelRoomType withoutTrashed()
 * @mixin \Eloquent
 */
class HotelRoomType extends BaseModel
{
}
