<?php

namespace App\Models\Activity;

use App\Models\BaseModel;

/**
 * App\Models\Activity\NewYearUserGoods
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $status 奖品状态：0-待发货，1-已发货, 2-确认收货
 * @property int $goods_id 奖品id
 * @property string $cover 奖品图片
 * @property string $name 奖品名称
 * @property int $luck_score 兑换消耗福气值
 * @property string|null $consignee 收件人姓名
 * @property string|null $mobile 收件人手机号
 * @property string|null $address 具体收货地址
 * @property string|null $ship_channel 快递公司名称
 * @property string|null $ship_code 快递公司编号
 * @property string|null $ship_sn 快递单号
 * @property string|null $ship_time 发货时间
 * @property string|null $confirm_time 确认收货时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods whereConfirmTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods whereConsignee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods whereLuckScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods whereShipChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods whereShipCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods whereShipSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods whereShipTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserGoods whereUserId($value)
 * @mixin \Eloquent
 */
class NewYearUserGoods extends BaseModel
{
}
