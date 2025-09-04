<?php

namespace App\Models;

use App\Models\Catering\CateringMerchant;
use App\Models\Catering\CateringShop;
use App\Models\Catering\CateringShopManager;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Laravel\Scout\Searchable;
use Tymon\JWTAuth\Contracts\JWTSubject;

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

    public function hotelMerchant()
    {
        return $this->hasOne(HotelMerchant::class, 'user_id')->where('status', 2);
    }

    public function hotelShop()
    {
        return $this->hasOne(HotelShop::class, 'user_id')->where('status', 1);
    }

    public function cateringMerchant()
    {
        return $this->hasOne(CateringMerchant::class, 'user_id')->where('status', 2);
    }

    public function cateringShop()
    {
        return $this->hasOne(CateringShop::class, 'user_id')->where('status', 1);
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class, 'user_id')->where('status', 2);
    }

    public function shop()
    {
        return $this->hasOne(Shop::class, 'user_id')->where('status', 1);
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
