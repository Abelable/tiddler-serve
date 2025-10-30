<?php

namespace App\Models;

/**
 * App\Models\PromoterComplaint
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $option_ids 选项ids
 * @property string $content 描述
 * @property string $imageList 凭证
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterComplaint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterComplaint newQuery()
 * @method static \Illuminate\Database\Query\Builder|PromoterComplaint onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterComplaint query()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterComplaint whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterComplaint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterComplaint whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterComplaint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterComplaint whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterComplaint whereOptionIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterComplaint whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterComplaint whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|PromoterComplaint withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PromoterComplaint withoutTrashed()
 * @mixin \Eloquent
 */
class PromoterComplaint extends BaseModel
{
}
