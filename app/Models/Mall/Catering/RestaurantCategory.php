<?php

namespace App\Models\Mall\Catering;

use App\Models\BaseModel;

/**
 * App\Models\Catering\RestaurantCategory
 *
 * @property int $id
 * @property string $name 餐馆分类名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|RestaurantCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|RestaurantCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|RestaurantCategory withoutTrashed()
 * @mixin \Eloquent
 */
class RestaurantCategory extends BaseModel
{
}
