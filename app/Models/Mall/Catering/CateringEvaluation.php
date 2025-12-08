<?php

namespace App\Models\Mall\Catering;

use App\Models\BaseModel;

/**
 * App\Models\Catering\CateringEvaluation
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $restaurant_id 餐饮门店id
 * @property float $score 餐饮门店评分
 * @property string $content 评论内容
 * @property string $image_list 评论图片
 * @property int $like_number 点赞数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|CateringEvaluation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringEvaluation newQuery()
 * @method static \Illuminate\Database\Query\Builder|CateringEvaluation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringEvaluation query()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringEvaluation whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringEvaluation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringEvaluation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringEvaluation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringEvaluation whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringEvaluation whereLikeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringEvaluation whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringEvaluation whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringEvaluation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringEvaluation whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|CateringEvaluation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CateringEvaluation withoutTrashed()
 * @mixin \Eloquent
 */
class CateringEvaluation extends BaseModel
{
}
