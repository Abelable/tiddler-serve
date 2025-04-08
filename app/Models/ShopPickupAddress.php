<?php

namespace App\Models;

/**
 * App\Models\ShopPickupAddress
 *
 * @property int $id
 * @property int $shop_id 店铺id
 * @property string $name 提货点名称
 * @property string $time_frame 提货时间范围
 * @property string $address_detail 提货点地址
 * @property string $longitude 提货点经度
 * @property string $latitude 提货点纬度
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPickupAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPickupAddress newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShopPickupAddress onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPickupAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPickupAddress whereAddressDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPickupAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPickupAddress whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPickupAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPickupAddress whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPickupAddress whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPickupAddress whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPickupAddress whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPickupAddress whereTimeFrame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopPickupAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ShopPickupAddress withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShopPickupAddress withoutTrashed()
 * @mixin \Eloquent
 */
class ShopPickupAddress extends BaseModel
{
}
