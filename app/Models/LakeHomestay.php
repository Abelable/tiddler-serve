<?php

namespace App\Models;

/**
 * App\Models\LakeHomestay
 *
 * @property int $id
 * @property int $hotel_id 民宿id
 * @property string $cover 民宿封面
 * @property string $name 民宿名称
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|LakeHomestay newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LakeHomestay newQuery()
 * @method static \Illuminate\Database\Query\Builder|LakeHomestay onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LakeHomestay query()
 * @method static \Illuminate\Database\Eloquent\Builder|LakeHomestay whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeHomestay whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeHomestay whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeHomestay whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeHomestay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeHomestay whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeHomestay whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LakeHomestay whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|LakeHomestay withTrashed()
 * @method static \Illuminate\Database\Query\Builder|LakeHomestay withoutTrashed()
 * @mixin \Eloquent
 */
class LakeHomestay extends BaseModel
{
}
