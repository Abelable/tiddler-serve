<?php

namespace App\Models\Mall\Hotel;

use App\Models\BaseModel;

/**
 * App\Models\HotelShopDepositChangeLog
 *
 * @property int $id
 * @property int $shop_id 店铺ID
 * @property int $change_type 变更类型：1-商家充值，2-平台扣除
 * @property string $old_balance 变更前金额
 * @property string $new_balance 变更后金额
 * @property string $change_amount 变更金额
 * @property string $reference_id 外部参考ID，例如微信支付单号、订单号
 * @property string $remark 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositChangeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HotelShopDepositChangeLog newQuery()
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
 * @mixin \Eloquent
 */
class HotelShopDepositChangeLog extends BaseModel
{
}
