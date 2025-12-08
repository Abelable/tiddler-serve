<?php

namespace App\Models\Mall\Scenic;

use App\Models\BaseModel;

/**
 * App\Models\ScenicCategory
 *
 * @property int $id
 * @property string $name 景点分类名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicCategory withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicCategory extends BaseModel
{
}
