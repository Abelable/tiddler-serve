<?php

namespace App\Models;

/**
 * App\Models\HotelShop
 *
 * @property int $id
 * @property int $status 状态：0-未支付保证金，1-已支付保证金
 * @property int $user_id 用户ID
 * @property int $merchant_id 商家ID
 * @property int $type 店铺类型：1-酒店官方，2-专营店，3-平台自营
 * @property string $deposit 店铺初始保证金
 * @property string $bg 店铺背景图
 * @property string $logo 店铺logo
 * @property string $name 店铺名称
 * @property string $owner_avatar 店主头像
 * @property string $owner_name 店主姓名
 * @property string $mobile 联系方式
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\HotelShopManager[] $managerList
 * @property-read int|null $manager_list_count
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereBg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereDeposit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereOwnerAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereOwnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShop whereUserId($value)
 * @mixin \Eloquent
 */
class HotelShop extends BaseModel
{
    public function managerList()
    {
        return $this->hasMany(HotelShopManager::class, 'shop_id');
    }
}
