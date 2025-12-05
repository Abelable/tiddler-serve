<?php

namespace App\Models;

/**
 * App\Models\OrderVerifyLog
 *
 * @property int $id
 * @property int $code_id 核销码ID
 * @property int $shop_id 核销店铺ID
 * @property int $verifier_id 核销人员ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog whereCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog whereVerifierId($value)
 * @mixin \Eloquent
 */
class OrderVerifyLog extends BaseModel
{
}
