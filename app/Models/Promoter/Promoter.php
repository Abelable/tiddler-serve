<?php

namespace App\Models\Promoter;

use App\Models\BaseModel;

/**
 * App\Models\Promoter
 *
 * @property int $id
 * @property int $status 状态：1-身份正常，2-身份即将失效（续身份窗口期），3-身份失效
 * @property int $user_id 用户id
 * @property int $level 代言人等级
 * @property int $scene 场景值，防串改，与等级对应「等级-场景值」：1-100, 2-201, 3-202, 4-203, 5-300
 * @property int $path 生成路径：1-管理后台添加，2-礼包购买
 * @property string $expiration_time 身份失效时间
 * @property int $order_id 订单id
 * @property int $gift_goods_id 礼包商品id-用于售后退款删除代言人身份
 * @property int $sub_user_number 下级人数
 * @property int $sub_promoter_number 下级代言人人数
 * @property float $self_commission_sum 累计自购佣金
 * @property float $share_commission_sum 累计分享佣金
 * @property float $team_commission_sum 累计团队佣金
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter newQuery()
 * @method static \Illuminate\Database\Query\Builder|Promoter onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter query()
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereExpirationTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereGiftGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereScene($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereSelfCommissionSum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereShareCommissionSum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereSubPromoterNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereSubUserNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereTeamCommissionSum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Promoter whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|Promoter withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Promoter withoutTrashed()
 * @mixin \Eloquent
 */
class Promoter extends BaseModel
{
}
