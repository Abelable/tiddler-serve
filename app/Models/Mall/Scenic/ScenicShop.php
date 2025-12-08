<?php

namespace App\Models\Mall\Scenic;

use App\Models\BaseModel;

/**
 * App\Models\ScenicShop
 *
 * @property int $id
 * @property int $status 状态：0-未支付保证金，1-已支付保证金
 * @property int $user_id 用户ID
 * @property int $merchant_id 商家ID
 * @property int $type 店铺类型：1-景区官方，2-旅行社，3-平台自营
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Mall\Scenic\ScenicShopManager[] $managerList
 * @property-read int|null $manager_list_count
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereBg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereDeposit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereOwnerAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereOwnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShop whereUserId($value)
 * @mixin \Eloquent
 */
class ScenicShop extends BaseModel
{
    public function managerList()
    {
        return $this->hasMany(ScenicShopManager::class, 'shop_id');
    }
}
