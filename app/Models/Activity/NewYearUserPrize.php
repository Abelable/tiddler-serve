<?php

namespace App\Models\Activity;

use App\Models\BaseModel;

/**
 * App\Models\Activity\NewYearUserPrize
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $prize_id 奖品id
 * @property int $prize_type 奖品类型：1-福气值，2-优惠券，3-商品
 * @property int $status 奖品状态：0-未使用，1-已使用
 * @property string $cover 奖品图片
 * @property string $name 奖品名称
 * @property int $coupon_id 优惠券id
 * @property int $goods_id 商品id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $ship_channel 快递公司名称
 * @property string|null $ship_code 快递公司编号
 * @property string|null $ship_sn 快递单号
 * @property string|null $ship_time 发货时间
 * @property string|null $confirm_time 确认收货时间
 * @property string|null $consignee 收件人姓名
 * @property string|null $mobile 收件人手机号
 * @property string|null $address 具体收货地址
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize query()
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereConfirmTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereConsignee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereCouponId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize wherePrizeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize wherePrizeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereShipChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereShipCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereShipSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereShipTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NewYearUserPrize whereUserId($value)
 * @mixin \Eloquent
 */
class NewYearUserPrize extends BaseModel
{
}
