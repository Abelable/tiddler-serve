<?php

namespace App\Models\Media\ShortVideo;

use App\Models\BaseModel;

/**
 * App\Models\ShortVideoCollection
 *
 * @property int $id
 * @property int $video_id 视频id
 * @property int $user_id 用户id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoCollection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoCollection newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShortVideoCollection onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoCollection query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoCollection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoCollection whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoCollection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoCollection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoCollection whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoCollection whereVideoId($value)
 * @method static \Illuminate\Database\Query\Builder|ShortVideoCollection withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShortVideoCollection withoutTrashed()
 * @mixin \Eloquent
 */
class ShortVideoCollection extends BaseModel
{
}
