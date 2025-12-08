<?php

namespace App\Models\Promoter;

use App\Models\BaseModel;

/**
 * App\Models\PromoterQa
 *
 * @property int $id
 * @property int $promoter_id 代言人id
 * @property int $user_id 用户id
 * @property string $question 问题
 * @property string $answer 答案
 * @property string $answer_time 回答时间
 * @property int $like_number 点赞数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterQa newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterQa newQuery()
 * @method static \Illuminate\Database\Query\Builder|PromoterQa onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterQa query()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterQa whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterQa whereAnswerTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterQa whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterQa whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterQa whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterQa whereLikeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterQa wherePromoterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterQa whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterQa whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterQa whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|PromoterQa withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PromoterQa withoutTrashed()
 * @mixin \Eloquent
 */
class PromoterQa extends BaseModel
{
}
