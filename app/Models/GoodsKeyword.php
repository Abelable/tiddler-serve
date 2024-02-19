<?php

namespace App\Models;

/**
 * App\Models\GoodsKeyword
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $content 搜索关键字内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsKeyword newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsKeyword newQuery()
 * @method static \Illuminate\Database\Query\Builder|GoodsKeyword onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsKeyword query()
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsKeyword whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsKeyword whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsKeyword whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsKeyword whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsKeyword whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GoodsKeyword whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|GoodsKeyword withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GoodsKeyword withoutTrashed()
 * @mixin \Eloquent
 */
class GoodsKeyword extends BaseModel
{
}
