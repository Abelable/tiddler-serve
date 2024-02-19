<?php

namespace App\Models;

/**
 * App\Models\MallKeyword
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $content 搜索关键字内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MallKeyword newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MallKeyword newQuery()
 * @method static \Illuminate\Database\Query\Builder|MallKeyword onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MallKeyword query()
 * @method static \Illuminate\Database\Eloquent\Builder|MallKeyword whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MallKeyword whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MallKeyword whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MallKeyword whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MallKeyword whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MallKeyword whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|MallKeyword withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MallKeyword withoutTrashed()
 * @mixin \Eloquent
 */
class MallKeyword extends BaseModel
{
}
