<?php

namespace App\Models;

/**
 * App\Models\ProviderHotel
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $provider_id 供应商id
 * @property int $hotel_id 酒店id
 * @property int $status 申请状态：0-待审核，1-审核通过，2-审核失败
 * @property string $failure_reason 审核失败原因
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderHotel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderHotel newQuery()
 * @method static \Illuminate\Database\Query\Builder|ProviderHotel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderHotel query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderHotel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderHotel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderHotel whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderHotel whereHotelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderHotel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderHotel whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderHotel whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderHotel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderHotel whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ProviderHotel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ProviderHotel withoutTrashed()
 * @mixin \Eloquent
 */
class ProviderHotel extends BaseModel
{
}
