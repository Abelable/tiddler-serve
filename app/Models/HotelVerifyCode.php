<?php

namespace App\Models;

/**
 * App\Models\HotelVerifyCode
 *
 * @property int $id
 * @property int $status 核销状态：0-待核销，1-已核销, 2-已失效
 * @property int $hotel_id 酒店id
 * @property int $order_id 订单id
 * @property string $code 核销码
 * @property string|null $expiration_time 核销码失效时间
 * @property int|null $verifier_id 核销人员ID
 * @property string|null $verify_time 核销时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|HotelVerifyCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelVerifyCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelVerifyCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelVerifyCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelVerifyCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelVerifyCode whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelVerifyCode whereExpirationTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelVerifyCode whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelVerifyCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelVerifyCode whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelVerifyCode whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelVerifyCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelVerifyCode whereVerifierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelVerifyCode whereVerifyTime($value)
 * @mixin \Eloquent
 */
class HotelVerifyCode extends BaseModel
{
    // 生成2开头的随机12位核销码
    public static function generateVerifyCode()
    {
        do {
            $code = rand(200000000000, 299999999999);
        } while (self::query()->where('code', $code)->exists());

        return $code;
    }
}
