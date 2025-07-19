<?php

namespace App\Models\Catering;

use App\Models\BaseModel;

/**
 * App\Models\Catering\CateringManager
 *
 * @property int $id
 * @property int $restaurant_id 餐饮门店id
 * @property int $manager_id 管理员id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|CateringManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringManager newQuery()
 * @method static \Illuminate\Database\Query\Builder|CateringManager onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringManager query()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringManager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringManager whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringManager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringManager whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringManager whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringManager whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|CateringManager withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CateringManager withoutTrashed()
 * @mixin \Eloquent
 */
class CateringManager extends BaseModel
{
}
