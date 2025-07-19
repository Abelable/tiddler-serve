<?php

namespace App\Models\Catering;

use App\Models\BaseModel;
use App\Models\User;

/**
 * App\Models\CateringAnswer
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
 * @method static \Illuminate\Database\Eloquent\Builder|CateringAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringAnswer newQuery()
 * @method static \Illuminate\Database\Query\Builder|CateringAnswer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringAnswer whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringAnswer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringAnswer whereLikeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringAnswer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringAnswer whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|CateringAnswer withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CateringAnswer withoutTrashed()
 * @mixin \Eloquent
 */
class CateringAnswer extends BaseModel
{
    public function userInfo()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'nickname', 'avatar');
    }
}
