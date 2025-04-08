<?php

namespace App\Models;

/**
 * App\Models\GoodsVerifyCode
 *
 * @property int $id
 * @property int $status 核销状态：0-待核销，1-已核销, 2-已失效
 * @property int $order_id 订单id
 * @property string $verify_code 核销码
 * @property string $expiration_time 核销码失效时间
 * @property int $verifier_id 核销人员id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsVerifyCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsVerifyCode newQuery()
 * @method static \Illuminate\Database\Query\Builder|GoodsVerifyCode onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsVerifyCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsVerifyCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsVerifyCode whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsVerifyCode whereExpirationTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsVerifyCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsVerifyCode whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsVerifyCode whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsVerifyCode whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsVerifyCode whereVerifierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsVerifyCode whereVerifyCode($value)
 * @method static \Illuminate\Database\Query\Builder|GoodsVerifyCode withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GoodsVerifyCode withoutTrashed()
 * @mixin \Eloquent
 */
class GoodsVerifyCode extends BaseModel
{
}
