<?php

namespace App\Models;

/**
 * App\Models\LiveRoom
 *
 * @property int $id
 * @property int $user_id 直播创建者id
 * @property int $status 直播状态：0-待开播(预告)，1-直播中，2-直播结束, 3-直播预告
 * @property string $title 直播标题
 * @property string $cover 直播封面
 * @property string $share_cover 直播间分享封面
 * @property int $direction 方向：1-竖屏，2-横屏
 * @property string $push_url 推流地址
 * @property string $play_url 拉流地址
 * @property string $playback_url 回放地址
 * @property string $group_id 群聊群组id
 * @property int $viewers_number 观看人数
 * @property int $praise_number 点赞数
 * @property string $notice_time 预告时间
 * @property string $start_time 开播时间
 * @property string $end_time 结束时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom newQuery()
 * @method static \Illuminate\Database\Query\Builder|LiveRoom onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom query()
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereNoticeTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom wherePlayUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom wherePlaybackUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom wherePraiseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom wherePushUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereShareCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LiveRoom whereViewersNumber($value)
 * @method static \Illuminate\Database\Query\Builder|LiveRoom withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LiveRoom withoutTrashed()
 * @mixin \Eloquent
 * @property-read \App\Models\User|null $anchorInfo
 */
class LiveRoom extends BaseModel
{
    public function anchorInfo()
    {
        return $this->belongsTo(User::class)->select('id', 'avatar', 'nickname');
    }
}
