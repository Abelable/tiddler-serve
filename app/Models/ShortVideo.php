<?php

namespace App\Models;

use Laravel\Scout\Searchable;

/**
 * App\Models\ShortVideo
 *
 * @property int $id
 * @property int $status 状态：0-待审核，1-审核通过
 * @property int $user_id 作者id
 * @property string $title 视频标题
 * @property string $cover 封面
 * @property string $video_url 视频地址
 * @property string $longitude 经度
 * @property string $latitude 纬度
 * @property string $address 具体地址
 * @property int $is_private 是否为私密视频：0-否，1-是
 * @property int $like_number 点赞数
 * @property int $comments_number 评论数
 * @property int $collection_times 收藏次数
 * @property int $share_times 分享次数
 * @property int $views 观看次数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $authorInfo
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShortVideo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereCollectionTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereCommentsNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereIsPrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereLikeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereShareTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereVideoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShortVideo whereViews($value)
 * @method static \Illuminate\Database\Query\Builder|ShortVideo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShortVideo withoutTrashed()
 * @mixin \Eloquent
 */
class ShortVideo extends BaseModel
{
    use Searchable;

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'authorName' => $this->authorInfo->nickname,
        ];
    }

    public function authorInfo()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'nickname', 'avatar');
    }
}
