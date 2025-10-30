<?php

namespace App\Models;

/**
 * App\Models\ComplaintOption
 *
 * @property int $id
 * @property int $type 类型：1-景点，2-酒店，3-餐饮门店，4-商品，5-代言人
 * @property string $title 标题
 * @property string $content 内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ComplaintOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComplaintOption newQuery()
 * @method static \Illuminate\Database\Query\Builder|ComplaintOption onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ComplaintOption query()
 * @method static \Illuminate\Database\Eloquent\Builder|ComplaintOption whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComplaintOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComplaintOption whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComplaintOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComplaintOption whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComplaintOption whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComplaintOption whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ComplaintOption withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ComplaintOption withoutTrashed()
 * @mixin \Eloquent
 */
class ComplaintOption extends BaseModel
{
}
