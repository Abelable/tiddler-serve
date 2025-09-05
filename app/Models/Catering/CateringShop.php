<?php

namespace App\Models\Catering;

use App\Models\BaseModel;

/**
 * App\Models\Catering\CateringShop
 *
 * @property int $id
 * @property int $status 状态：0-未支付保证金，1-已支付保证金
 * @property int $user_id 用户id
 * @property int $merchant_id 服务商id
 * @property int $type 店铺类型：1-餐饮官方，2-专营店，3-平台自营
 * @property float $deposit 店铺保证金
 * @property string $owner_avatar 店主头像
 * @property string $owner_name 店主姓名
 * @property string $mobile 联系方式
 * @property string $bg 店铺背景图
 * @property string $logo 店铺logo
 * @property string $name 店铺名称
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Catering\CateringShopManager[] $managerList
 * @property-read int|null $manager_list_count
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShop newQuery()
 * @method static \Illuminate\Database\Query\Builder|CateringShop onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShop query()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShop whereBg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShop whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShop whereDeposit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShop whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShop whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShop whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShop whereOwnerAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShop whereOwnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShop whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShop whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShop whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|CateringShop withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CateringShop withoutTrashed()
 * @mixin \Eloquent
 */
class CateringShop extends BaseModel
{
    public function managerList()
    {
        return $this->hasMany(CateringShopManager::class, 'shop_id');
    }
}
