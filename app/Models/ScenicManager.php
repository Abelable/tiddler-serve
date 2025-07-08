<?php

namespace App\Models;

/**
 * App\Models\ScenicManager
 *
 * @property int $id
 * @property int $scenic_id 景点id
 * @property int $manager_id 管理员id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicManager newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicManager onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicManager query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicManager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicManager whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicManager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicManager whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicManager whereScenicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicManager whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicManager withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicManager withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicManager extends BaseModel
{
}
