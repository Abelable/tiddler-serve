<?php

namespace App\Models;

/**
 * App\Models\HotelShopDepositChangeLog
 *
 * @property int $id
 * @property int $shop_id 店铺id
 * @property float $old_balance 变更前金额
 * @property float $new_balance 变更后金额
 * @property float $change_amount 变更金额
 * @property int $change_type 变更类型：1-商家充值，2-平台扣除
 * @property string $reference_id 外部参考ID，如订单号
 * @property string $remark 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositChangeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositChangeLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|HotelShopDepositChangeLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositChangeLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositChangeLog whereChangeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositChangeLog whereChangeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositChangeLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositChangeLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositChangeLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositChangeLog whereNewBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositChangeLog whereOldBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositChangeLog whereReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositChangeLog whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositChangeLog whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositChangeLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|HotelShopDepositChangeLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|HotelShopDepositChangeLog withoutTrashed()
 * @mixin \Eloquent
 */
class HotelShopDepositChangeLog extends BaseModel
{
}
