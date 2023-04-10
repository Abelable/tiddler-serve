<?php

namespace App\Models;

/**
 * App\Models\ScenicProject
 *
 * @property int $id
 * @property int $scenic_id 景点id
 * @property string $image 项目图片
 * @property string $name 项目名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProject newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicProject onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProject query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProject whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProject whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProject whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProject whereScenicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicProject whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicProject withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicProject withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicProject extends BaseModel
{
}
