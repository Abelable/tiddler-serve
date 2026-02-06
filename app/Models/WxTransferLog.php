<?php

namespace App\Models;

/**
 * App\Models\WxTransferLog
 *
 * @property int $id
 * @property int $status 奖品状态：0-已创建，待用户确认，1-转账成功, 2-转账失败
 * @property string|null $fail_reason 失败原因
 * @property int $user_id 用户id
 * @property string $openid 用户openid
 * @property string $out_bill_no 商户转账单号
 * @property string|null $transfer_bill_no 微信转账单号
 * @property string $transfer_scene_id 转账场景ID
 * @property string $transfer_amount 转账金额
 * @property string|null $transfer_title 转账标题
 * @property string|null $transfer_content 转账内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|WxTransferLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WxTransferLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WxTransferLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|WxTransferLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxTransferLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxTransferLog whereFailReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxTransferLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxTransferLog whereOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxTransferLog whereOutBillNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxTransferLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxTransferLog whereTransferAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxTransferLog whereTransferBillNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxTransferLog whereTransferContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxTransferLog whereTransferSceneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxTransferLog whereTransferTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxTransferLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WxTransferLog whereUserId($value)
 * @mixin \Eloquent
 */
class WxTransferLog extends BaseModel
{
}
