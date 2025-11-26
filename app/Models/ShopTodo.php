<?php

namespace App\Models;

/**
 * App\Models\ShopTodo
 *
 * @property int $id
 * @property int $shop_id 店铺id
 * @property int $status 状态：0-待处理，1-已处理
 * @property int $type 类型：1-待发货，2-售后
 * @property string $reference_id 外部参考ID，如订单ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShopTodo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopTodo newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShopTodo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopTodo query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopTodo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopTodo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopTodo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopTodo whereReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopTodo whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopTodo whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopTodo whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopTodo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ShopTodo withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShopTodo withoutTrashed()
 * @mixin \Eloquent
 */
class ShopTodo extends BaseModel
{
}
