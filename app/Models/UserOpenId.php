<?php

namespace App\Models;

/**
 * App\Models\UserOpenId
 *
 * @property int $id
 * @property int $user_id 关联用户ID
 * @property string $openid 小程序openid
 * @property string $app_id 小程序AppID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserOpenId newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserOpenId newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserOpenId query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserOpenId whereAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOpenId whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOpenId whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOpenId whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOpenId whereOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOpenId whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOpenId whereUserId($value)
 * @mixin \Eloquent
 */
class UserOpenId extends BaseModel
{
    protected $fillable = [
        'user_id',
        'app_id',
        'openid',
    ];
}
