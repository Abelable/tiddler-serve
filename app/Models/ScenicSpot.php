<?php

namespace App\Models;

use Laravel\Scout\Searchable;

/**
 * App\Models\ScenicSpot
 *
 * @property int $id
 * @property int $category_id 景区分类id
 * @property string $name 景区名称
 * @property string $level 景区等级
 * @property string $longitude 经度
 * @property string $latitude 纬度
 * @property string $address 具体地址
 * @property float $score 景区评分
 * @property string $video 视频
 * @property string $image_list 图片列表
 * @property string $brief 简介
 * @property string $open_time_list 开放时间
 * @property string $policy_list 优待政策
 * @property string $hotline_list 景区热线
 * @property string $project_list 景区项目
 * @property string $facility_list 景区设施
 * @property string $tips_list 游玩贴士
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicSpot onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot whereBrief($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot whereFacilityList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot whereHotlineList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot whereImageList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot whereOpenTimeList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot wherePolicyList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot whereProjectList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot whereTipsList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicSpot whereVideo($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicSpot withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicSpot withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicSpot extends BaseModel
{
    use Searchable;

    /**
     * 索引的字段
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return $this->only('id', 'name', 'brief');
    }
}
