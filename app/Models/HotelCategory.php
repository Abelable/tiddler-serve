<?php

namespace App\Models;

/**
 * App\Models\HotelCategory
 *
 * @property int $id
 * @property string $name 酒店分类名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|HotelCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelCategory newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|HotelCategory withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelCategory withoutTrashed()
 * @mixin \Eloquent
 */
class HotelCategory extends BaseModel
{
}
