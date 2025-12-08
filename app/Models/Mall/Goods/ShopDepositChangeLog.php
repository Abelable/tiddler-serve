<?php

namespace App\Models\Mall\Goods;

use App\Models\BaseModel;

/**
 * App\Models\ShopDepositChangeLog
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
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereChangeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereChangeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereNewBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereOldBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopDepositChangeLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ShopDepositChangeLog extends BaseModel
{
}
