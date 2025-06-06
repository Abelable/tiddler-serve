<?php

namespace App\Models;

/**
 * App\Models\GiftType
 *
 * @property int $id
 * @property int $status 状态: 1-显示,2-隐藏
 * @property string $name 礼包类型名称
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|GiftType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GiftType newQuery()
 * @method static \Illuminate\Database\Query\Builder|GiftType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|GiftType query()
 * @method static \Illuminate\Database\Eloquent\Builder|GiftType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftType whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftType whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GiftType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|GiftType withTrashed()
 * @method static \Illuminate\Database\Query\Builder|GiftType withoutTrashed()
 * @mixin \Eloquent
 */
class GiftType extends BaseModel
{
}
