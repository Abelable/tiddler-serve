<?php

namespace App\Models;

/**
 * App\Models\HotelOrderVerifyCode
 *
 * @property int $id
 * @property int $status 核销状态：0-待核销，1-已核销, 2-已失效
 * @property int $order_id 订单id
 * @property string $code 核销码
 * @property string|null $expiration_time 核销码失效时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode whereExpirationTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelOrderVerifyCode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HotelOrderVerifyCode extends BaseModel
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
