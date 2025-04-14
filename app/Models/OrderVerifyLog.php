<?php

namespace App\Models;

/**
 * App\Models\OrderVerifyLog
 *
 * @property int $id
 * @property int $verify_code_id 核销码id
 * @property string $verify_time 核销时间
 * @property int $shop_id 核销店铺id
 * @property int $verifier_id 核销人员id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog whereVerifierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog whereVerifyCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyLog whereVerifyTime($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Query\Builder|OrderVerifyLog onlyTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrderVerifyLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrderVerifyLog withoutTrashed()
 */
class OrderVerifyLog extends BaseModel
{
}
