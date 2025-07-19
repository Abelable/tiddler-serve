<?php

namespace App\Models\Catering;

use App\Models\BaseModel;
use Laravel\Scout\Searchable;

/**
 * App\Models\Catering\Restaurant
 *
 * @property int $id
 * @property int $category_id 餐馆分类id
 * @property string $name 餐馆名称
 * @property float $price 餐馆最低价格
 * @property string $video 视频
 * @property string $cover 餐馆封面图片
 * @property string $food_image_list 菜品图片列表
 * @property string $environment_image_list 环境图片列表
 * @property string $price_image_list 价目表图片列表
 * @property string $longitude 经度
 * @property string $latitude 纬度
 * @property string $address 具体地址
 * @property float $taste_rate 餐馆口味评分
 * @property float $environment_rate 餐馆环境评分
 * @property float $service_rate 餐馆服务评分
 * @property float $food_rate 餐馆食材评分
 * @property string $tel_list 餐馆联系电话
 * @property string $open_time_list 餐馆营业时间
 * @property string $facility_list 服务设施列表
 * @property int $sales_volume 销量
 * @property float $score 评分
 * @property int $views 点击率
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant newQuery()
 * @method static \Illuminate\Database\Query\Builder|Restaurant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereEnvironmentImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereEnvironmentRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereFacilityList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereFoodImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereFoodRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereOpenTimeList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant wherePriceImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereSalesVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereServiceRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereTasteRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereTelList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereVideo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurant whereViews($value)
 * @method static \Illuminate\Database\Query\Builder|Restaurant withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Restaurant withoutTrashed()
 * @mixin \Eloquent
 */
class Restaurant extends BaseModel
{
    use Searchable;

    /**
     * 索引的字段
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return $this->only('id', 'name');
    }

    public function mealTicketIds(): array
    {
        return $this
            ->hasMany(MealTicketRestaurant::class, 'restaurant_id')
            ->pluck('meal_ticket_id')
            ->toArray();
    }

    public function setMealIds(): array
    {
        return $this
            ->hasMany(SetMealRestaurant::class, 'restaurant_id')
            ->pluck('set_meal_id')
            ->toArray();
    }
}
