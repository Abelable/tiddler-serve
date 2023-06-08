<?php

namespace App\Models;

/**
 * App\Models\ProviderScenicSpot
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $provider_id 供应商id
 * @property int $scenic_id 景点id
 * @property int $status 申请状态：0-待审核，1-审核通过，2-审核失败
 * @property string $failure_reason 审核失败原因
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderScenicSpot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderScenicSpot newQuery()
 * @method static \Illuminate\Database\Query\Builder|ProviderScenicSpot onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderScenicSpot query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderScenicSpot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderScenicSpot whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderScenicSpot whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderScenicSpot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderScenicSpot whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderScenicSpot whereScenicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderScenicSpot whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderScenicSpot whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProviderScenicSpot whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|ProviderScenicSpot withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ProviderScenicSpot withoutTrashed()
 * @mixin \Eloquent
 */
class ProviderScenicSpot extends BaseModel
{
}
