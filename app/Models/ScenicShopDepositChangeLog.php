<?php

namespace App\Models;

/**
 * App\Models\ScenicShopDepositChangeLog
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
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositChangeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositChangeLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositChangeLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositChangeLog whereChangeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositChangeLog whereChangeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositChangeLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositChangeLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositChangeLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositChangeLog whereNewBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositChangeLog whereOldBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositChangeLog whereReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositChangeLog whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositChangeLog whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositChangeLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ScenicShopDepositChangeLog extends BaseModel
{
}
