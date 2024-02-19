<?php

namespace App\Models;

/**
 * App\Models\Keyword
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $content 搜索关键字内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword newQuery()
 * @method static \Illuminate\Database\Query\Builder|Keyword onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword query()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Keyword withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Keyword withoutTrashed()
 * @mixin \Eloquent
 */
class Keyword extends BaseModel
{
}
