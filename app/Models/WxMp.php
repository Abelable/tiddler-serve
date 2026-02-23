<?php

namespace App\Models;

/**
 * App\Models\WxMp
 *
 * @property int $id
 * @property string $app_id
 * @property string $secret
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|WxMp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WxMp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WxMp query()
 * @method static \Illuminate\Database\Eloquent\Builder|WxMp whereAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxMp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxMp whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxMp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxMp whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxMp whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxMp whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WxMp extends BaseModel
{
}
