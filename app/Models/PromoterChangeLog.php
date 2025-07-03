<?php

namespace App\Models;

/**
 * App\Models\PromoterChangeLog
 *
 * @property int $id
 * @property int $promoter_id 代言人id
 * @property int $change_type 变更类型：1-身份升级，2-有效期变更
 * @property int $old_level 旧等级
 * @property int $new_level 新等级
 * @property string $old_expiration_time 旧失效时间
 * @property string $new_expiration_time 新失效时间
 * @property int $old_gift_goods_id 旧家乡好物id
 * @property int $new_gift_goods_id 新家乡好物id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterChangeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterChangeLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|PromoterChangeLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterChangeLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterChangeLog whereChangeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterChangeLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterChangeLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterChangeLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterChangeLog whereNewExpirationTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterChangeLog whereNewGiftGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterChangeLog whereNewLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterChangeLog whereOldExpirationTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterChangeLog whereOldGiftGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterChangeLog whereOldLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterChangeLog wherePromoterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PromoterChangeLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|PromoterChangeLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PromoterChangeLog withoutTrashed()
 * @mixin \Eloquent
 */
class PromoterChangeLog extends BaseModel
{
}
