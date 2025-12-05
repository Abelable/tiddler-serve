<?php

namespace App\Models;

/**
 * App\Models\HotelOrderVerifyLog
 *
 * @property int $id
 * @property int $code_id 核销码ID
 * @property int $hotel_id 核销酒店id
 * @property int $verifier_id 核销人员id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyLog whereCodeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyLog whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyLog whereVerifierId($value)
 * @mixin \Eloquent
 */
class HotelOrderVerifyLog extends BaseModel
{
}
