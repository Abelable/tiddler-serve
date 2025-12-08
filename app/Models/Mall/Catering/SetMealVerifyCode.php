<?php

namespace App\Models\Mall\Catering;

use App\Models\BaseModel;

/**
 * App\Models\Catering\SetMealVerifyCode
 *
 * @property int $id
 * @property int $status 核销状态：0-待核销，1-已核销, 2-已失效
 * @property int $shop_id 餐饮门店id
 * @property int $order_id 订单id
 * @property string $code 核销码
 * @property string|null $expiration_time 核销码失效时间
 * @property int|null $verifier_id 核销人员ID
 * @property string|null $verify_time 核销时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyCode whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyCode whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyCode whereExpirationTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyCode whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyCode whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyCode whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyCode whereVerifierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetMealVerifyCode whereVerifyTime($value)
 * @mixin \Eloquent
 */
class SetMealVerifyCode extends BaseModel
{
    // 生成6开头的随机12位核销码
    public static function generateVerifyCode()
    {
        do {
            $code = rand(600000000000, 699999999999);
        } while (self::query()->where('code', $code)->exists());

        return $code;
    }
}
