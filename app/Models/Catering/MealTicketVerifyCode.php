<?php

namespace App\Models\Catering;

use App\Models\BaseModel;

/**
 * App\Models\Catering\MealTicketVerifyCode
 *
 * @property int $id
 * @property int $status 核销状态：0-待核销，1-已核销, 2-已失效
 * @property int $order_id 订单id
 * @property string $code 核销码
 * @property string|null $expiration_time 核销码失效时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyCode whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyCode whereExpirationTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyCode whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyCode whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealTicketVerifyCode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MealTicketVerifyCode extends BaseModel
{
    // 生成5开头的随机12位核销码
    public static function generateVerifyCode()
    {
        do {
            $code = rand(500000000000, 599999999999);
        } while (self::query()->where('code', $code)->exists());

        return $code;
    }
}
