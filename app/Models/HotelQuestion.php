<?php

namespace App\Models;

/**
 * App\Models\HotelQuestion
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $hotel_id 酒店id
 * @property string $content 提问内容
 * @property int $answer_num 评论数量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\HotelAnswer[] $answerList
 * @property-read int|null $answer_list_count
 * @method static \Illuminate\Database\Eloquent\Builder|HotelQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelQuestion newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelQuestion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelQuestion whereAnswerNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelQuestion whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelQuestion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelQuestion whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelQuestion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelQuestion whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|HotelQuestion withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelQuestion withoutTrashed()
 * @mixin \Eloquent
 */
class HotelQuestion extends BaseModel
{
    public function answerList()
    {
        return $this->hasMany(HotelAnswer::class, 'question_id');
    }

    public function answersTotal()
    {
        return $this->answerList()->count();
    }

    public function firstAnswer()
    {
        return $this->answerList()->orderBy('created_at', 'asc')->first();
    }
}
