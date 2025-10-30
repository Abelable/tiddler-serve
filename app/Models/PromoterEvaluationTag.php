<?php

namespace App\Models;

/**
 * App\Models\PromoterEvaluationTag
 *
 * @property int $id
 * @property int $promoter_id 代言人id
 * @property int $tag_id 标签id
 * @property int $evaluation_id 评论id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluationTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluationTag newQuery()
 * @method static \Illuminate\Database\Query\Builder|PromoterEvaluationTag onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluationTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluationTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluationTag whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluationTag whereEvaluationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluationTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluationTag wherePromoterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluationTag whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterEvaluationTag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|PromoterEvaluationTag withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PromoterEvaluationTag withoutTrashed()
 * @mixin \Eloquent
 */
class PromoterEvaluationTag extends BaseModel
{
}
