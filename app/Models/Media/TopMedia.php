<?php

namespace App\Models\Media;

use App\Models\BaseModel;

/**
 * App\Models\TopMedia
 *
 * @property int $id
 * @property int $media_type 媒体类型
 * @property int $media_id 媒体id
 * @property string $cover 封面
 * @property string $title 标题
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TopMedia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TopMedia newQuery()
 * @method static \Illuminate\Database\Query\Builder|TopMedia onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TopMedia query()
 * @method static \Illuminate\Database\Eloquent\Builder|TopMedia whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TopMedia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TopMedia whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TopMedia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TopMedia whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TopMedia whereMediaType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TopMedia whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TopMedia whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|TopMedia withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TopMedia withoutTrashed()
 * @mixin \Eloquent
 */
class TopMedia extends BaseModel
{
}
