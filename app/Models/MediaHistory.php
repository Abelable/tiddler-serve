<?php

namespace App\Models;

/**
 * App\Models\MediaHistory
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $media_type 媒体类型
 * @property int $media_id 媒体id
 * @property int $count 次数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MediaHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaHistory newQuery()
 * @method static \Illuminate\Database\Query\Builder|MediaHistory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|MediaHistory whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaHistory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaHistory whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaHistory whereMediaType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MediaHistory whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|MediaHistory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|MediaHistory withoutTrashed()
 * @mixin \Eloquent
 */
class MediaHistory extends BaseModel
{
}
