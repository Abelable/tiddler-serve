<?php

namespace App\Models\Mall\Scenic;

use App\Models\BaseModel;

/**
 * App\Models\ScenicEvaluation
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $scenic_id 景点id
 * @property float $score 景点评分
 * @property string $content 评论内容
 * @property string $image_list 评论图片
 * @property int $like_number 点赞数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicEvaluation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicEvaluation newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicEvaluation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicEvaluation query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicEvaluation whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicEvaluation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicEvaluation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicEvaluation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicEvaluation whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicEvaluation whereLikeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicEvaluation whereScenicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicEvaluation whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicEvaluation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicEvaluation whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicEvaluation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicEvaluation withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicEvaluation extends BaseModel
{
}
