<?php

namespace App\Models;

/**
 * App\Models\ScenicVerifyCode
 *
 * @property int $id
 * @property int $status 核销状态：0-待核销，1-已核销, 2-已失效
 * @property int $scenic_id 景点id
 * @property int $order_id 订单id
 * @property string $code 核销码
 * @property string|null $expiration_time 核销码失效时间
 * @property int|null $verifier_id 核销人员ID
 * @property string|null $verify_time 核销时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicVerifyCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicVerifyCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicVerifyCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicVerifyCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicVerifyCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicVerifyCode whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicVerifyCode whereExpirationTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicVerifyCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicVerifyCode whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicVerifyCode whereScenicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicVerifyCode whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicVerifyCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicVerifyCode whereVerifierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicVerifyCode whereVerifyTime($value)
 * @mixin \Eloquent
 */
class ScenicVerifyCode extends BaseModel
{
    // 生成1开头的随机12位核销码
    public static function generateVerifyCode()
    {
        do {
            $code = rand(100000000000, 199999999999);
        } while (self::query()->where('code', $code)->exists());

        return $code;
    }
}
