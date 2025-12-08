<?php

namespace App\Models\Media\ShortVideo;

use App\Models\BaseModel;
use App\Models\User;

/**
 * App\Models\ShortVideoComment
 *
 * @property int $id
 * @property int $video_id 短视频id
 * @property int $parent_id 回复评论id
 * @property int $user_id 用户id
 * @property string $content 评论内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $userInfo
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShortVideoComment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereVideoId($value)
 * @method static \Illuminate\Database\Query\Builder|ShortVideoComment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShortVideoComment withoutTrashed()
 * @mixin \Eloquent
 */
class ShortVideoComment extends BaseModel
{
    public function userInfo()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'nickname', 'avatar');
    }
}
