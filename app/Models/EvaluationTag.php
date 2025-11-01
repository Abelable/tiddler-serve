<?php

namespace App\Models;

/**
 * App\Models\EvaluationTag
 *
 * @property int $id
 * @property int $scene 使用场景：1-景点，2-酒店，3-餐饮门店，4-商品，5-代言人
 * @property int $type 类型：1-正向标签（好评类），2-正向标签（好评类），3-负向标签（差评类）
 * @property string $content 内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationTag newQuery()
 * @method static \Illuminate\Database\Query\Builder|EvaluationTag onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationTag whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationTag whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationTag whereScene($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationTag whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationTag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EvaluationTag withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EvaluationTag withoutTrashed()
 * @mixin \Eloquent
 */
class EvaluationTag extends BaseModel
{
}
