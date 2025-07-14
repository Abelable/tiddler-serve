<?php

namespace App\Models;

/**
 * App\Models\HotelManager
 *
 * @property int $id
 * @property int $hotel_id 酒店id
 * @property int $manager_id 管理员id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|HotelManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelManager newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelManager onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelManager query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelManager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelManager whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelManager whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelManager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelManager whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelManager whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|HotelManager withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelManager withoutTrashed()
 * @mixin \Eloquent
 */
class HotelManager extends BaseModel
{
}
