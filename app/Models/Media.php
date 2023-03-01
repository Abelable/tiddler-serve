<?php

namespace App\Models;

/**
 * App\Models\Media
 *
 * @property int $id
 * @property int $user_id 作者id
 * @property int $type 媒体类型：1-直播（只存直播、预告），2-短视频，3-短图文（攻略）
 * @property int $media_id 媒体id
 * @property int $viewers_number 观看人数
 * @property int $praise_number 点赞数
 * @property int $comments_number 评论数
 * @property int $collection_times 收藏次数
 * @property int $share_times 分享次数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Media newQuery()
 * @method static \Illuminate\Database\Query\Builder|Media onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Media query()
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereCollectionTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereCommentsNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media wherePraiseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereShareTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereViewersNumber($value)
 * @method static \Illuminate\Database\Query\Builder|Media withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Media withoutTrashed()
 * @mixin \Eloquent
 */
class Media extends BaseModel
{
}
