<?php

namespace App\Models\Task;

use App\Models\BaseModel;

/**
 * App\Models\UserTask
 *
 * @property int $id
 * @property int $status 任务状态：1-进行中，2-已完成，待领取奖励，3-领取审核中，4-已领取，5-领取失败，6-已取消
 * @property int $step 任务进度
 * @property int $user_id 用户id
 * @property int $task_id 任务id
 * @property string $task_reward 任务奖励
 * @property int $merchant_type 商家类型：1-景点，2-酒店，3-餐饮，4-电商
 * @property int|null $merchant_id 商家id
 * @property int|null $order_id 订单id
 * @property int|null $product_id 产品id
 * @property int|null $product_type 产品类型：5-餐券，6-套餐
 * @property string|null $pick_time 领取时间
 * @property string|null $finish_time 完成时间
 * @property int|null $withdrawal_id 提现记录id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereFinishTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereMerchantType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask wherePickTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereStep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereTaskReward($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTask whereWithdrawalId($value)
 * @mixin \Eloquent
 */
class UserTask extends BaseModel
{
}
