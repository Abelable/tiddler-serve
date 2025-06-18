<?php

namespace App\Models;

/**
 * App\Models\ShopDeposit
 *
 * @property int $id
 * @property int $status 账户状态：1-正常，2-异常
 * @property int $shop_id 店铺id
 * @property float $balance 保证金余额
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDeposit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDeposit newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShopDeposit onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDeposit query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDeposit whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDeposit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDeposit whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDeposit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDeposit whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDeposit whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDeposit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ShopDeposit withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShopDeposit withoutTrashed()
 * @mixin \Eloquent
 */
class ShopDeposit extends BaseModel
{
}
