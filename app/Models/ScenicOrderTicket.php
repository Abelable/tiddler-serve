<?php

namespace App\Models;

/**
 * App\Models\ScenicOrderTicket
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property int $order_id 订单id
 * @property int $ticket_id 门票id
 * @property string $name 门票名称
 * @property int $category_id 门票分类id
 * @property string $category_name 门票分类
 * @property string $selected_date_timestamp 选中日期时间戳
 * @property float $price 门票价格
 * @property float $sales_commission_rate 销售佣金比例
 * @property float $promotion_commission_rate 推广佣金比例%
 * @property float $promotion_commission_upper_limit 推广佣金上限
 * @property float $superior_promotion_commission_rate 上级推广佣金比例%
 * @property float $superior_promotion_commission_upper_limit 上级推广佣金上限
 * @property int $number 门票数量
 * @property int $effective_time 生效时间，单位小时
 * @property int $validity_time 有效期, 单位天
 * @property int $refund_status 退票状态：1-随时可退，2-有条件退，3-不可退
 * @property int $need_exchange 是否需要换票：0-无需换票，1-需要换票
 * @property string $scenic_list 关联景点列表
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicOrderTicket onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket whereCategoryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket whereEffectiveTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket whereNeedExchange($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket wherePromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket wherePromotionCommissionUpperLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket whereRefundStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket whereSalesCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket whereScenicList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket whereSelectedDateTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket whereSuperiorPromotionCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket whereSuperiorPromotionCommissionUpperLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicOrderTicket whereValidityTime($value)
 * @method static \Illuminate\Database\Query\Builder|ScenicOrderTicket withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicOrderTicket withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicOrderTicket extends BaseModel
{

}
