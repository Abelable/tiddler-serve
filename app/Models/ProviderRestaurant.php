<?php

namespace App\Models;

/**
 * App\Models\ProviderRestaurant
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $provider_id 服务商id
 * @property int $restaurant_id 餐馆id
 * @property int $status 申请状态：0-待审核，1-审核通过，2-审核失败
 * @property string $failure_reason 审核失败原因
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderRestaurant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderRestaurant newQuery()
 * @method static \Illuminate\Database\Query\Builder|ProviderRestaurant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderRestaurant query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderRestaurant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderRestaurant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderRestaurant whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderRestaurant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderRestaurant whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderRestaurant whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderRestaurant whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderRestaurant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderRestaurant whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ProviderRestaurant withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ProviderRestaurant withoutTrashed()
 * @mixin \Eloquent
 */
class ProviderRestaurant extends BaseModel
{
}
