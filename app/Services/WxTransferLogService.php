<?php

namespace App\Services;

use App\Models\WxTransferLog;

class WxTransferLogService extends BaseService
{
    public function createLog($userId, $openId, $outBillNo, $transferBillNo, $sceneId, $amount, $title, $content)
    {
        $log = WxTransferLog::new();
        $log->user_id = $userId;
        $log->openid = $openId;
        $log->out_bill_no = $outBillNo;
        $log->transfer_bill_no = $transferBillNo;
        $log->transfer_scene_id = $sceneId;
        $log->transfer_amount = $amount;
        $log->transfer_title = $title;
        $log->transfer_content = $content;
        $log->save();
        return $log;
    }

    public function updateStatus($outBillNo, $status = 1, $failReason = '')
    {
        WxTransferLog::query()
            ->where('status', 0)
            ->where('out_bill_no', $outBillNo)
            ->update(['status' => $status, 'fail_reason' => $failReason]);
    }

    public function getLogByOutBillNo($outBillNo, $columns = ['*'])
    {
        return WxTransferLog::query()->where('out_bill_no', $outBillNo)->first($columns);
    }
}
