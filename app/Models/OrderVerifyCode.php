<?php

namespace App\Models;

/**
 * App\Models\OrderVerifyCode
 *
 * @property int $id
 * @property int $status 核销状态：0-待核销，1-已核销, 2-已失效
 * @property int $order_id 订单id
 * @property string $code 核销码
 * @property string $expiration_time 核销码失效时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyCode newQuery()
 * @method static \Illuminate\Database\Query\Builder|OrderVerifyCode onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyCode whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyCode whereExpirationTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyCode whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyCode whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderVerifyCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|OrderVerifyCode withTrashed()
 * @method static \Illuminate\Database\Query\Builder|OrderVerifyCode withoutTrashed()
 * @mixin \Eloquent
 */
class OrderVerifyCode extends BaseModel
{
    // 生成随机8位核销码
    public static function generateVerifyCode()
    {
        do {
            $code = rand(10000000, 99999999);
        } while (self::query()->where('code', $code)->exists());

        return $code;
    }
}
