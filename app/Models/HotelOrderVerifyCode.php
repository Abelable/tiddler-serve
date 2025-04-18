<?php

namespace App\Models;

/**
 * App\Models\HotelOrderVerifyCode
 *
 * @property int $id
 * @property int $status 核销状态：0-待核销，1-已核销, 2-已失效
 * @property int $order_id 订单id
 * @property int $hotel_id 酒店id
 * @property string $code 核销码
 * @property string $expiration_time 核销码失效时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelOrderVerifyCode onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode whereExpirationTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|HotelOrderVerifyCode withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelOrderVerifyCode withoutTrashed()
 * @mixin \Eloquent
 */
class HotelOrderVerifyCode extends BaseModel
{
    // 生成随机11位核销码
    public static function generateVerifyCode()
    {
        do {
            $code = rand(10000000000, 99999999999);
        } while (self::query()->where('code', $code)->exists());

        return $code;
    }
}
