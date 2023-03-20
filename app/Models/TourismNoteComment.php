<?php

namespace App\Models;

/**
 * App\Models\TourismNoteComment
 *
 * @property int $id
 * @property int $note_id 攻略笔记id
 * @property int $parent_id 回复评论id
 * @property int $user_id 用户id
 * @property string $content 评论内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $userInfo
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment newQuery()
 * @method static \Illuminate\Database\Query\Builder|TourismNoteComment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment whereNoteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TourismNoteComment whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|TourismNoteComment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TourismNoteComment withoutTrashed()
 * @mixin \Eloquent
 */
class TourismNoteComment extends BaseModel
{
    public function userInfo()
    {
        return $this->belongsTo(User::class)->select('id', 'nickname', 'avatar');
    }
}
