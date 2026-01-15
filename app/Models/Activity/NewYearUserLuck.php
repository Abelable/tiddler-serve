<?php

namespace App\Models\Activity;

use App\Models\BaseModel;

/**
 * App\Models\Activity\NewYearUserLuck
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $score 福气值
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserLuck newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserLuck newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserLuck query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserLuck whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserLuck whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserLuck whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserLuck whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserLuck whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserLuck whereUserId($value)
 * @mixin \Eloquent
 */
class NewYearUserLuck extends BaseModel
{
}
