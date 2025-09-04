<?php

namespace App\Models;

/**
 * App\Models\Shop
 *
 * @property int $id
 * @property int $status 状态：0-未支付保证金，1-已支付保证金
 * @property int $user_id 用户id
 * @property int $merchant_id 商家id
 * @property int $type 店铺类型：1-个人，2-企业
 * @property float $deposit 店铺保证金
 * @property string $category_ids 店铺分类id
 * @property string $bg 店铺背景图
 * @property string $logo 店铺logo
 * @property string $name 店铺名称
 * @property string $brief 店铺简称
 * @property string $owner_name 店主姓名
 * @property string $mobile 联系方式
 * @property string $address_detail 店铺地址详情
 * @property string $longitude 店铺经度
 * @property string $latitude 店铺纬度
 * @property string $open_time_list 店铺营业时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ShopManager[] $managerList
 * @property-read int|null $manager_list_count
 * @method static \Illuminate\Database\Eloquent\Builder|Shop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shop newQuery()
 * @method static \Illuminate\Database\Query\Builder|Shop onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Shop query()
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereAddressDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereBg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereBrief($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereCategoryIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereDeposit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereOpenTimeList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereOwnerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shop whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Shop withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Shop withoutTrashed()
 * @mixin \Eloquent
 */
class Shop extends BaseModel
{
    public function managerList()
    {
        return $this->hasMany(ShopManager::class, 'shop_id');
    }
}
