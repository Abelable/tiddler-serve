<?php

namespace App\Models;

/**
 * App\Models\ScenicOrderVerifyLog
 *
 * @property int $id
 * @property int $order_id 订单id
 * @property int $scenic_id 核销景点id
 * @property int $verifier_id 核销人员id
 * @property string $verify_time 核销时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicOrderVerifyLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog whereScenicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog whereVerifierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog whereVerifyTime($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicOrderVerifyLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicOrderVerifyLog withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicOrderVerifyLog extends BaseModel
{
}
