<?php

namespace App\Models;

/**
 * App\Models\ShopDepositChangeLog
 *
 * @property int $id
 * @property int $shop_id 店铺id
 * @property float $old_deposit 变更前金额
 * @property float $new_deposit 变更后金额
 * @property float $change_amount 变更金额
 * @property int $change_type 变更类型：1-平台扣除，2-商家充值
 * @property string $remark 备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|ShopDepositChangeLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereChangeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereChangeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereNewDeposit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereOldDeposit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|ShopDepositChangeLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ShopDepositChangeLog withoutTrashed()
 * @mixin \Eloquent
 */
class ShopDepositChangeLog extends BaseModel
{
}
