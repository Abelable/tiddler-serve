<?php

namespace App\Models;

/**
 * App\Models\SystemTodo
 *
 * @property int $id
 * @property int $status 状态：0-待处理，1-已处理
 * @property int $type 类型
 * @property string $reference_id 外部参考ID，如订单ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SystemTodo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemTodo newQuery()
 * @method static \Illuminate\Database\Query\Builder|SystemTodo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemTodo query()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemTodo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemTodo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemTodo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemTodo whereReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemTodo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemTodo whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemTodo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SystemTodo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SystemTodo withoutTrashed()
 * @mixin \Eloquent
 */
class SystemTodo extends BaseModel
{
}
