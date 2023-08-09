<?php

namespace App\Models;

/**
 * App\Models\Hotel
 *
 * @property int $id
 * @property int $status 申请状态：0-待审核，1-审核通过，2-审核未通过
 * @property string $failure_reason 审核失败原因
 * @property int $category_id 酒店分类id
 * @property string $name 酒店名称
 * @property int $grade 酒店等级：1-经济，2-舒适，3-高档，4-豪华
 * @property float $price 酒店最低价格
 * @property float $longitude 经度
 * @property float $latitude 纬度
 * @property string $address 具体地址
 * @property float $rate 酒店评分
 * @property string $video 视频
 * @property string $image_list 图片列表
 * @property string $feature_tag_list 酒店特点
 * @property string $opening_year 开业年份
 * @property string $last_decoration_year 最近一次装修年份
 * @property int $room_num 房间数量
 * @property string $tel 酒店联系电话
 * @property string $brief 简介
 * @property string $facility_list 酒店设施
 * @property string $service_list 酒店服务
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
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereBrief($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereCheckInTipList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereFacilityList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereFeatureTagList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereLastDecorationYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereOpeningYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel wherePreorderTipList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereRemindList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereRoomNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereServiceList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hotel whereVideo($value)
 * @method static \Illuminate\Database\Query\Builder|Hotel withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Hotel withoutTrashed()
 * @mixin \Eloquent
 */
class Hotel extends BaseModel
{
}
