<?php

namespace App\Models;

/**
 * App\Models\UserCenter
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $bg 用户中心背景图
 * @property string $signature 用户签名
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserCenter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCenter newQuery()
 * @method static \Illuminate\Database\Query\Builder|UserCenter onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCenter query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCenter whereBg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCenter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCenter whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCenter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCenter whereSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCenter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCenter whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|UserCenter withTrashed()
 * @method static \Illuminate\Database\Query\Builder|UserCenter withoutTrashed()
 * @mixin \Eloquent
 */
class UserCenter extends BaseModel
{
}
