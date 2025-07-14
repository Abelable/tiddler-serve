<?php

namespace App\Models;

/**
 * App\Models\HotelShopDeposit
 *
 * @property int $id
 * @property int $status 账户状态：1-正常，2-异常
 * @property int $shop_id 店铺id
 * @property float $balance 保证金余额
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDeposit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDeposit newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelShopDeposit onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDeposit query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDeposit whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDeposit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDeposit whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDeposit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDeposit whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDeposit whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDeposit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|HotelShopDeposit withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelShopDeposit withoutTrashed()
 * @mixin \Eloquent
 */
class HotelShopDeposit extends BaseModel
{
}
