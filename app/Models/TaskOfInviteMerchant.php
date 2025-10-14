<?php

namespace App\Models;

/**
 * App\Models\TaskOfInviteMerchant
 *
 * @property int $id
 * @property int $status 任务状态：1-进行中，2-已领取，3-已完成，4-已下架
 * @property int $product_type 产品类型：1-景点，2-酒店，3-餐饮，4-电商
 * @property int $product_id 产品id
 * @property string $product_name 产品名称
 * @property string $tel 联系电话
 * @property string $address 具体地址
 * @property string $longitude 经度
 * @property string $latitude 纬度
 * @property float $reward_total 任务奖励总和
 * @property string $reward_list 任务阶段奖励
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|TaskOfInviteMerchant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskOfInviteMerchant newQuery()
 * @method static \Illuminate\Database\Query\Builder|TaskOfInviteMerchant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskOfInviteMerchant query()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskOfInviteMerchant whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskOfInviteMerchant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskOfInviteMerchant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskOfInviteMerchant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskOfInviteMerchant whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskOfInviteMerchant whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskOfInviteMerchant whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskOfInviteMerchant whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskOfInviteMerchant whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskOfInviteMerchant whereRewardList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskOfInviteMerchant whereRewardTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskOfInviteMerchant whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskOfInviteMerchant whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskOfInviteMerchant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|TaskOfInviteMerchant withTrashed()
 * @method static \Illuminate\Database\Query\Builder|TaskOfInviteMerchant withoutTrashed()
 * @mixin \Eloquent
 */
class TaskOfInviteMerchant extends BaseModel
{
}
