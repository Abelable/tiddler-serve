<?php

namespace App\Models;

/**
 * App\Models\CateringQuestion
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $restaurant_id 餐饮门店id
 * @property string $content 提问内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|CateringQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringQuestion newQuery()
 * @method static \Illuminate\Database\Query\Builder|CateringQuestion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringQuestion whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringQuestion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringQuestion whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringQuestion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringQuestion whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|CateringQuestion withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CateringQuestion withoutTrashed()
 * @mixin \Eloquent
 */
class CateringQuestion extends BaseModel
{
}
