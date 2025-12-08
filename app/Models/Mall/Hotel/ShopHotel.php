<?php

namespace App\Models\Mall\Hotel;

use App\Models\BaseModel;

/**
 * App\Models\ShopHotel
 *
 * @property int $id
 * @property int $shop_id 店铺id
 * @property int $hotel_id 酒店id
 * @property int $status 申请状态：0-待审核，1-审核通过，2-审核失败
 * @property string $failure_reason 审核失败原因
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShopHotel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopHotel newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShopHotel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopHotel query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopHotel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopHotel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopHotel whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopHotel whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopHotel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopHotel whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopHotel whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopHotel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ShopHotel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShopHotel withoutTrashed()
 * @mixin \Eloquent
 */
class ShopHotel extends BaseModel
{
}
