<?php

namespace App\Models\Catering;

use App\Models\BaseModel;

/**
 * App\Models\Catering\CateringShopDeposit
 *
 * @property int $id
 * @property int $status 账户状态：1-正常，2-异常
 * @property int $shop_id 店铺ID
 * @property string $balance 保证金余额
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDeposit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDeposit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDeposit query()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDeposit whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDeposit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDeposit whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDeposit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDeposit whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDeposit whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDeposit whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CateringShopDeposit extends BaseModel
{
}
