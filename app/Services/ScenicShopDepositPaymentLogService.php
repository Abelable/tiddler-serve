<?php

namespace App\Services;

use App\Models\ScenicShopDepositPaymentLog;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\Log;

class ScenicShopDepositPaymentLogService extends BaseService
{
    public function createLog(int $userId, int $providerId, int $shopId, float $paymentAmount)
    {
        $log = ScenicShopDepositPaymentLog::new();
        $log->user_id = $userId;
        $log->provider_id = $providerId;
        $log->shop_id = $shopId;
        $log->payment_amount = $paymentAmount;
        $log->save();
        return $log;
    }

    public function wxPaySuccess(array $data)
    {
        $shopId = $data['attach'] ? str_replace('scenic_shop_id:', '', $data['attach']) : '';
        $payId = $data['transaction_id'] ?? '';
        $actualPaymentAmount = $data['total_fee'] ? bcdiv($data['total_fee'], 100, 2) : 0;

        $log = $this->getLogByShopId($shopId);
        if (is_null($log)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '店铺保证金支付记录不存在');
        }
        if (bccomp($actualPaymentAmount, $log->payment_amount, 2) != 0) {
            $errMsg = "支付回调异常，店铺保证金支付记录{$log->id}金额不一致，请检查，支付回调金额：{$actualPaymentAmount}，记录金额：{$log->payment_amount}";
            Log::error($errMsg);
            $this->throwBusinessException(CodeResponse::FAIL, $errMsg);
        }

        $log->status = 1;
        $log->pay_id = $payId;
        $log->pay_time = now()->format('Y-m-d\TH:i:s');
        $log->save();

        return $log;
    }

    public function getLogByShopId($shopId, $columns = ['*'])
    {
        return ScenicShopDepositPaymentLog::query()->where('shop_id', $shopId)->first($columns);
    }

    public function getLogByProviderId($providerId, $columns = ['*'])
    {
        return ScenicShopDepositPaymentLog::query()
            ->where('provider_id', $providerId)
            ->where('status', 1)
            ->first($columns);
    }

    public function getLogPage(PageInput $input, $columns = ['*'])
    {
        return ScenicShopDepositPaymentLog::query()
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getLogListByShopIds(array $shopIds, $columns = ['*'])
    {
        return ScenicShopDepositPaymentLog::query()->whereIn('shop_id', $shopIds)->get($columns);
    }
}
