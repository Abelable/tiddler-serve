<?php

namespace App\Models;

/**
 * App\Models\TourismNote
 *
 * @property int $id
 * @property int $status 状态：0-待审核，1-审核通过
 * @property int $user_id 作者id
 * @property string $image_list 主图图片列表
 * @property string $title 标题
 * @property string $content 内容
 * @property int $goods_id 商品id
 * @property float $longitude 经度
 * @property float $latitude 纬度
 * @property string $address 具体地址
 * @property int $is_private 是否为私密视频：0-否，1-是
 * @property int $like_number 点赞数
 * @property int $comments_number 评论数
 * @property int $collection_times 收藏次数
 * @property int $share_times 分享次数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $authorInfo
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TourismNoteComment[] $commentList
 * @property-read int|null $comment_list_count
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote newQuery()
 * @method static \Illuminate\Database\Query\Builder|TourismNote onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote query()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereCollectionTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereCommentsNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereIsPrivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereLikeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereShareTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNote whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|TourismNote withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TourismNote withoutTrashed()
 * @mixin \Eloquent
 * @property-read \App\Models\Goods|null $goodsInfo
 */
class TourismNote extends BaseModel
{
    public function commentList()
    {
        return $this->hasMany(TourismNoteComment::class, 'note_id');
    }

    public function authorInfo()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'nickname', 'avatar');
    }

    public function goodsInfo()
    {
        return $this->belongsTo(Goods::class, 'goods_id')->select('id', 'name', 'image', 'price', 'market_price', 'stock');
    }
}
