<?php

namespace App\Models;

/**
 * App\Models\ScenicShopDepositChangeLog
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
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositChangeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScenicShopDepositChangeLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|ScenicShopDepositChangeLog onlyTrashed()
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
 * @method static \Illuminate\Database\Query\Builder|ScenicShopDepositChangeLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ScenicShopDepositChangeLog withoutTrashed()
 * @mixin \Eloquent
 */
class ScenicShopDepositChangeLog extends BaseModel
{
}
