<?php

namespace App\Models;

/**
 * App\Models\CateringShopDepositChangeLog
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
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositChangeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositChangeLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|CateringShopDepositChangeLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositChangeLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositChangeLog whereChangeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositChangeLog whereChangeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositChangeLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositChangeLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositChangeLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositChangeLog whereNewBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositChangeLog whereOldBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositChangeLog whereReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositChangeLog whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositChangeLog whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CateringShopDepositChangeLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|CateringShopDepositChangeLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CateringShopDepositChangeLog withoutTrashed()
 * @mixin \Eloquent
 */
class CateringShopDepositChangeLog extends BaseModel
{
}
