<?php

namespace App\Models;

/**
 * App\Models\ShopRefundAddress
 *
 * @property int $id
 * @property int $shop_id 用户id
 * @property string $consignee_name 收货人姓名
 * @property string $mobile 手机号
 * @property string $address_detail 收获地址
 * @property string $supplement 补充说明
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRefundAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRefundAddress newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShopRefundAddress onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRefundAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRefundAddress whereAddressDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRefundAddress whereConsigneeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRefundAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRefundAddress whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRefundAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRefundAddress whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRefundAddress whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRefundAddress whereSupplement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopRefundAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ShopRefundAddress withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShopRefundAddress withoutTrashed()
 * @mixin \Eloquent
 */
class ShopRefundAddress extends BaseModel
{
}
