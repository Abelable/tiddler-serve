<?php

namespace App\Models;

/**
 * App\Models\ShortVideoPraise
 *
 * @property int $id
 * @property int $video_id 视频id
 * @property int $user_id 用户id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoPraise newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoPraise newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShortVideoPraise onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoPraise query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoPraise whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoPraise whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoPraise whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoPraise whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoPraise whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoPraise whereVideoId($value)
 * @method static \Illuminate\Database\Query\Builder|ShortVideoPraise withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShortVideoPraise withoutTrashed()
 * @mixin \Eloquent
 */
class ShortVideoPraise extends BaseModel
{
}
