<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $avatar 用户头像图片
 * @property string $nickname 用户昵称或网络名称
 * @property string $mobile 用户手机号码
 * @property string $openid 小程序openid
 * @property int $gender 性别：0-未知，1-男，2-女
 * @property string $bg 背景图
 * @property string $birthday 生日
 * @property string $constellation 星座
 * @property string $career 职业
 * @property string $signature 签名
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\AuthInfo|null $authInfo
 * @property-read \App\Models\CateringProvider|null $cateringProvider
 * @property-read \App\Models\HotelProvider|null $hotelProvider
 * @property-read \App\Models\Merchant|null $merchant
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Promoter|null $promoterInfo
 * @property-read \App\Models\ScenicMerchant|null $scenicMerchant
 * @property-read \App\Models\ScenicShop|null $scenicShop
 * @property-read \App\Models\Shop|null $shop
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ShopManager[] $shopManagerList
 * @property-read int|null $shop_manager_list_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Query\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBirthday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCareer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereConstellation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|User withoutTrashed()
 * @mixin \Eloquent
 */
class User extends BaseModel implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory, Notifiable, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'openid'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'nickname' => $this->nickname,
            'signature' => $this->signature,
        ];
    }

    public function promoterInfo()
    {
        return $this
            ->hasOne(Promoter::class, 'user_id')
            ->whereIn('status', [1, 2]);
    }

    public function superiorId() {
        return $this->hasOne(Relation::class, 'user_id')->value('superior_id');
    }

    public function scenicMerchant()
    {
        return $this->hasOne(ScenicMerchant::class, 'user_id')->where('status', 2);
    }

    public function scenicShop()
    {
        return $this->hasOne(ScenicShop::class, 'user_id')->where('status', 1);
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class, 'user_id')->where('status', 2);
    }

    public function shop()
    {
        return $this->hasOne(Shop::class, 'user_id')->where('status', 1);
    }

    public function shopManagerList()
    {
        return $this->hasMany(ShopManager::class, 'user_id');
    }

    public function hotelProvider()
    {
        return $this->hasOne(HotelProvider::class, 'user_id')->where('status', 2);
    }

    public function cateringProvider()
    {
        return $this->hasOne(CateringProvider::class, 'user_id')->where('status', 2);
    }

    public function authInfo()
    {
        return $this->hasOne(AuthInfo::class, 'user_id')->where('status', 1);
    }

    public function followedUsersNumber()
    {
        return $this->hasMany(Fan::class, 'fan_id')->count();
    }

    public function fansNumber()
    {
        return $this->hasMany(Fan::class, 'author_id')->count();
    }

    public static function generateMobile()
    {
        do {
            $mobile = rand(20000000000, 99999999999);
        } while (self::query()->where('mobile', $mobile)->exists());

        return $mobile;
    }
}
