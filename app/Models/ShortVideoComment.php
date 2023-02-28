<?php

namespace App\Models;

/**
 * App\Models\ShortVideoComment
 *
 * @property int $id
 * @property int $video_id 短视频id
 * @property int $comment_id 回复评论id
 * @property int $user_id 用户id
 * @property string $content 评论内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShortVideoComment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideoComment whereVideoId($value)
 * @method static \Illuminate\Database\Query\Builder|ShortVideoComment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShortVideoComment withoutTrashed()
 * @mixin \Eloquent
 */
class ShortVideoComment extends BaseModel
{
}
