<?php

namespace App\Models\Activity;

use App\Models\BaseModel;

/**
 * App\Models\Activity\NewYearTask
 *
 * @property int $id
 * @property int $status 状态：1-进行中；2-已下架
 * @property string $icon 任务图标
 * @property string $name 任务名称
 * @property string $desc 任务描述
 * @property string $btn_content 按钮内容
 * @property int $luck_score 任务福气值
 * @property int $type 任务类型：1-页面跳转, 2-加群
 * @property string $param 任务参数，例如页面路径
 * @property int $sort 排序
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearTask query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearTask whereBtnContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearTask whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearTask whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearTask whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearTask whereLuckScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearTask whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearTask whereParam($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearTask whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearTask whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearTask whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearTask whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class NewYearTask extends BaseModel
{
}
