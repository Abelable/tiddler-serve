<?php

namespace App\Models;

/**
 * App\Models\PromoterEvaluation
 *
 * @property int $id
 * @property int $promoter_id 代言人id
 * @property int $user_id 用户id
 * @property float $score 评分
 * @property string $content 内容
 * @property string $image_list 图片
 * @property int $like_number 点赞数
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluation newQuery()
 * @method static \Illuminate\Database\Query\Builder|PromoterEvaluation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluation query()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluation whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluation whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluation whereLikeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluation wherePromoterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluation whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluation whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|PromoterEvaluation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PromoterEvaluation withoutTrashed()
 * @mixin \Eloquent
 */
class PromoterEvaluation extends BaseModel
{
}
