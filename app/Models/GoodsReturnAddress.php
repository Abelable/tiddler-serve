<?php

namespace App\Models;

/**
 * App\Models\GoodsReturnAddress
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $consignee_name 收货人姓名
 * @property string $mobile 手机号
 * @property string $address_detail 收获地址
 * @property string $supplement 补充说明
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress newQuery()
 * @method static \Illuminate\Database\Query\Builder|GoodsReturnAddress onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress whereAddressDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress whereConsigneeName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress whereSupplement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsReturnAddress whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|GoodsReturnAddress withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GoodsReturnAddress withoutTrashed()
 * @mixin \Eloquent
 */
class GoodsReturnAddress extends BaseModel
{

}
