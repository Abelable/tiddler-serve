<?php

namespace App\Utils;

use App\Services\WxTransferLogService;
use Illuminate\Support\Facades\Log;
use Yansongda\LaravelPay\Facades\Pay;
use Yansongda\Pay\Exceptions\GatewayException;

class WxTransferServe
{
    public static function new()
    {
        return new static();
    }

    public function __construct()
    {

    }

    public function transfer($sceneId, $userId, $openId, $amount, $title, $content)
    {
        $outBillNo = date('YmdHis') . random_int(10000, 99999);
        $params = [
            '_action' => 'mch_transfer',
            'out_bill_no' => $outBillNo,
            'transfer_scene_id' => $sceneId,
            'openid' => $openId,
            'transfer_amount' => (int) bcmul($amount, 100, 0),
            'transfer_remark' => $content,
            'transfer_scene_report_infos' => [
                ['info_type' => '岗位类型', 'info_content' => $title],
                ['info_type' => '报酬说明', 'info_content' => $content],
            ],
        ];

        try {
            $transfer = Pay::wechat()->transfer($params);
            Log::info('wx_transfer', $transfer->toArray());
        } catch (GatewayException|\Throwable $e) {
            // 微信业务错误（余额不足 / 参数错误 / 风控等）
            Log::warning('wx_transfer_fail', [
                'out_bill_no' => $outBillNo,
                'msg' => $e->getMessage(),
            ]);
            throw new \Exception('微信转账失败：' . $e->getMessage());
        }

        WxTransferLogService::getInstance()->createLog(
            $userId,
            $openId,
            $outBillNo,
            $transfer->get('transfer_bill_no') ?? '',
            $sceneId,
            $amount,
            $title,
            $content,
        );

        return [
            'appId' => env('WX_MP_APPID'),
            'mchId' => env('WX_PAY_MCH_ID'),
            'outBillNo' => $outBillNo,
            'package' => $transfer->get('package_info') ?? '',
        ];
    }

    public function transferFail($outBillNo, $failReason = '')
    {
        $params = [
            '_action' => 'mch_transfer',
            'out_bill_no' => $outBillNo,
        ];

        try {
            $result = Pay::wechat()->cancel($params);
            Log::info('wx_cancel_transfer', $result->toArray());
        } catch (GatewayException|\Throwable $e) {
            // 微信业务错误（余额不足 / 参数错误 / 风控等）
            Log::warning('wx_cancel_transfer_fail', [
                'out_bill_no' => $outBillNo,
                'msg' => $e->getMessage(),
            ]);
            throw new \Exception('微信取消转账失败：' . $e->getMessage());
        }

        WxTransferLogService::getInstance()->updateStatus($outBillNo, 2, $failReason);
    }
}
