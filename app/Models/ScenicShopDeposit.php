<?php

namespace App\Models;

/**
 * App\Models\ScenicShopDeposit
 *
 * @property int $id
 * @property int $status 账户状态：1-正常，2-异常
 * @property int $shop_id 店铺id
 * @property float $balance 保证金余额
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDeposit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDeposit newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicShopDeposit onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDeposit query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDeposit whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDeposit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDeposit whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDeposit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDeposit whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDeposit whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDeposit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicShopDeposit withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicShopDeposit withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicShopDeposit extends BaseModel
{
}
