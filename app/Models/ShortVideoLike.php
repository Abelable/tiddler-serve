<?php

namespace App\Models;

/**
 * App\Models\ShortVideoLike
 *
 * @property int $id
 * @property int $video_id 视频id
 * @property int $user_id 用户id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoLike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoLike newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShortVideoLike onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoLike query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoLike whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoLike whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoLike whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoLike whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoLike whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoLike whereVideoId($value)
 * @method static \Illuminate\Database\Query\Builder|ShortVideoLike withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShortVideoLike withoutTrashed()
 * @mixin \Eloquent
 */
class ShortVideoLike extends BaseModel
{
}
