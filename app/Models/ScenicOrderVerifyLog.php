<?php

namespace App\Models;

/**
 * App\Models\ScenicOrderVerifyLog
 *
 * @property int $id
 * @property int $code_id 核销码ID
 * @property int $scenic_id 核销景点id
 * @property int $verifier_id 核销人员id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog whereCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog whereScenicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyLog whereVerifierId($value)
 * @mixin \Eloquent
 */
class ScenicOrderVerifyLog extends BaseModel
{
}
