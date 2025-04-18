<?php

namespace App\Models;

/**
 * App\Models\ScenicOrderVerifyCode
 *
 * @property int $id
 * @property int $status 核销状态：0-待核销，1-已核销, 2-已失效
 * @property int $order_id 订单id
 * @property int $scenic_id 景点id
 * @property string $code 核销码
 * @property string $expiration_time 核销码失效时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyCode newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicOrderVerifyCode onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyCode whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyCode whereExpirationTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyCode whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyCode whereScenicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyCode whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderVerifyCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicOrderVerifyCode withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicOrderVerifyCode withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicOrderVerifyCode extends BaseModel
{
    // 生成随机12位核销码
    public static function generateVerifyCode()
    {
        do {
            $code = rand(100000000000, 999999999999);
        } while (self::query()->where('code', $code)->exists());

        return $code;
    }
}
