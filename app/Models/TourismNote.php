<?php

namespace App\Models;

/**
 * App\Models\TourismNote
 *
 * @property int $id
 * @property int $user_id 作者id
 * @property string $image_list 主图图片列表
 * @property string $title 标题
 * @property string $content 内容
 * @property int $viewers_number 观看人数
 * @property int $praise_number 点赞数
 * @property int $comments_number 评论数
 * @property int $collection_times 收藏次数
 * @property int $share_times 分享次数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote newQuery()
 * @method static \Illuminate\Database\Query\Builder|TourismNote onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote query()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereCollectionTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereCommentsNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote wherePraiseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereShareTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereViewersNumber($value)
 * @method static \Illuminate\Database\Query\Builder|TourismNote withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TourismNote withoutTrashed()
 * @mixin \Eloquent
 */
class TourismNote extends BaseModel
{
}
