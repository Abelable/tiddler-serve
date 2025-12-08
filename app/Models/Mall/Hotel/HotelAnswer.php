<?php

namespace App\Models\Mall\Hotel;

use App\Models\BaseModel;
use App\Models\User;

/**
 * App\Models\HotelAnswer
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $question_id 问题id
 * @property string $content 回答内容
 * @property int $like_number 点赞数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $userInfo
 * @method static \Illuminate\Database\Eloquent\Builder|HotelAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelAnswer newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelAnswer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelAnswer whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelAnswer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelAnswer whereLikeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelAnswer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelAnswer whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|HotelAnswer withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelAnswer withoutTrashed()
 * @mixin \Eloquent
 */
class HotelAnswer extends BaseModel
{
    public function userInfo()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'nickname', 'avatar');
    }
}
