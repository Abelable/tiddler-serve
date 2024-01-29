<?php

namespace App\Models;

use Laravel\Scout\Searchable;

/**
 * App\Models\Hotel
 *
 * @property int $id
 * @property int $category_id 酒店分类id
 * @property string $name 酒店名称
 * @property string $english_name 酒店英文名称
 * @property int $grade 酒店等级：1-经济，2-舒适，3-高档，4-豪华
 * @property float $price 酒店最低价格
 * @property string $video 视频
 * @property string $cover 封面图片
 * @property string $appearance_image_list 外观图片列表
 * @property string $interior_image_list 内景图片列表
 * @property string $room_image_list 房间图片列表
 * @property string $environment_image_list 环境图片列表
 * @property string $restaurant_image_list 餐厅图片列表
 * @property string $longitude 经度
 * @property string $latitude 纬度
 * @property string $address 具体地址
 * @property float $rate 酒店评分
 * @property string $feature_tag_list 酒店特点
 * @property string $opening_year 开业年份
 * @property string $last_decoration_year 最近一次装修年份
 * @property int $room_num 房间数量
 * @property string $tel 酒店联系电话
 * @property string $brief 简介
 * @property string $recreation_facility 娱乐设施
 * @property string $health_facility 康体设施
 * @property string $children_facility 儿童设施
 * @property string $common_facility 通用设施
 * @property string $public_area_facility 公共区设施
 * @property string $traffic_service 交通服务
 * @property string $catering_service 餐饮服务
 * @property string $reception_service 前台服务
 * @property string $clean_service 清洁服务
 * @property string $business_service 商务服务
 * @property string $other_service 其他服务
 * @property string $remind_list 酒店政策-重要提醒
 * @property string $check_in_tip_list 酒店政策-入住必读
 * @property string $preorder_tip_list 酒店政策-预定须知
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel newQuery()
 * @method static \Illuminate\Database\Query\Builder|Hotel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel query()
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereAppearanceImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereBrief($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereBusinessService($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereCateringService($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereCheckInTipList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereChildrenFacility($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereCleanService($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereCommonFacility($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereEnglishName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereEnvironmentImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereFeatureTagList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereHealthFacility($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereInteriorImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereLastDecorationYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereOpeningYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereOtherService($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel wherePreorderTipList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel wherePublicAreaFacility($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereReceptionService($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereRecreationFacility($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereRemindList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereRestaurantImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereRoomImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereRoomNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereTrafficService($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereVideo($value)
 * @method static \Illuminate\Database\Query\Builder|Hotel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Hotel withoutTrashed()
 * @mixin \Eloquent
 */
class Hotel extends BaseModel
{
    use Searchable;

    /**
     * 索引的字段
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return $this->only('id', 'name', 'english_name', 'brief');
    }
}
