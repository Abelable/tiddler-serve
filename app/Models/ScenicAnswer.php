<?php

namespace App\Models;

/**
 * App\Models\ScenicAnswer
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $question_id 问题id
 * @property string $content 回答内容
 * @property int $like_number 点赞数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicAnswer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicAnswer newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicAnswer onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicAnswer query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicAnswer whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicAnswer whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicAnswer whereLikeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicAnswer whereQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicAnswer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicAnswer whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicAnswer withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicAnswer withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicAnswer extends BaseModel
{
}
