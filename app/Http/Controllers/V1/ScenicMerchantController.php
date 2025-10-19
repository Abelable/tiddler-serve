<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\ScenicMerchantService;
use App\Services\ScenicShopDepositPaymentLogService;
use App\Services\ScenicShopDepositService;
use App\Services\ScenicShopService;
use App\Services\UserTaskService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ScenicMerchantInput;
use Illuminate\Support\Facades\DB;

class ScenicMerchantController extends Controller
{
    public function settleIn()
    {
        /** @var ScenicMerchantInput $input */
        $input = ScenicMerchantInput::new();

        $inviterId = $this->verifyId('inviterId');
        $taskId = $this->verifyId('taskId');

        $merchant = ScenicMerchantService::getInstance()->getMerchantByUserId($this->userId());

        if (!is_null($merchant)) {
            if ($merchant->status == 3) {
                $merchant->status = 0;
                $merchant->failure_reason = '';
                ScenicMerchantService::getInstance()->updateMerchant($merchant, $input);
            } else {
                return $this->fail(CodeResponse::DATA_EXISTED, '您已提交店铺申请，请勿重复提交');
            }
        } else {
            DB::transaction(function () use ($taskId, $inviterId, $input) {
                $merchant = ScenicMerchantService::getInstance()->createMerchant($input, $this->userId());
                $shop = ScenicShopService::getInstance()->createShop($this->userId(), $merchant->id, $input);
                ScenicShopDepositPaymentLogService::getInstance()
                    ->createLog($this->userId(), $merchant->id, $shop->id, $shop->deposit);
                ScenicShopDepositService::getInstance()->createShopDeposit($shop->id);

                // 邀请商家入驻活动
                if ($inviterId) {
                    $userTask = UserTaskService::getInstance()->getUserTaskByStatus($inviterId, $taskId, [1]);
                    if (!is_null($userTask)) {
                        $userTask->step = 1;
                        $userTask->merchant_id = $merchant->id;
                        $userTask->save();
                    }
                }
            });
        }

        return $this->success();
    }

    public function status()
    {
        $merchant = ScenicMerchantService::getInstance()->getMerchantByUserId($this->userId());
        // todo 目前一个商家对应一个店铺，暂时可以用商家id获取店铺，之后一个商家有多个店铺，需要传入店铺id
        $shop = ScenicShopService::getInstance()->getShopByUserId($this->userId());

        return $this->success($merchant ? [
            'id' => $merchant->id,
            'status' => $merchant->status,
            'failureReason' => $merchant->failure_reason,
            'deposit' => $shop->deposit,
            'shopId' => $shop->id
        ] : null);
    }

    public function info()
    {
        $merchant = ScenicMerchantService::getInstance()->getMerchantByUserId($this->userId());
        if (!is_null($merchant)) {
            $shop = ScenicShopService::getInstance()->getShopByUserId($this->userId());
            $merchant['shopType'] = $shop->type;
            $merchant['deposit'] = $shop->deposit;
            $merchant['shopBg'] = $shop->bg;
            $merchant['shopLogo'] = $shop->logo;
            $merchant['shopName'] = $shop->name;
        }
        return $this->success($merchant);
    }

    public function delete()
    {
        $merchant = ScenicMerchantService::getInstance()->getMerchantByUserId($this->userId());
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '景区服务商信息不存在');
        }
        $merchant->delete();
        return $this->success();
    }
}
