<?php

namespace App\Models;

/**
 * App\Models\ScenicQuestion
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $scenic_id 景点id
 * @property string $content 提问内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicQuestion newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicQuestion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicQuestion whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicQuestion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicQuestion whereScenicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicQuestion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicQuestion whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicQuestion withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicQuestion withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicQuestion extends BaseModel
{
}
