<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\ScenicMerchantService;
use App\Services\ScenicShopDepositService;
use App\Services\ScenicShopService;
use App\Services\SystemTodoService;
use App\Services\UserTaskService;
use App\Utils\CodeResponse;
use App\Utils\Enums\TodoEnums;
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
            if ($merchant->status != 3) {
                return $this->fail(CodeResponse::DATA_EXISTED, '您已提交店铺申请，请勿重复提交');
            }

            DB::transaction(function () use ($merchant, $input) {
                $merchant->status = 0;
                $merchant->failure_reason = '';
                ScenicMerchantService::getInstance()->updateMerchant($merchant, $input);

                // todo 商家入驻通知
                SystemTodoService::getInstance()->createTodo(TodoEnums::SCENIC_MERCHANT_NOTICE, [$merchant->id]);
            });
        } else {
            DB::transaction(function () use ($taskId, $inviterId, $input) {
                $merchant = ScenicMerchantService::getInstance()->createMerchant($input, $this->userId());
                $shop = ScenicShopService::getInstance()->createShop($this->userId(), $merchant->id, $input);
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

                // todo 商家入驻通知
                SystemTodoService::getInstance()->createTodo(TodoEnums::SCENIC_MERCHANT_NOTICE, [$merchant->id]);
            });
        }

        return $this->success();
    }

    public function status()
    {
        $merchant = ScenicMerchantService::getInstance()->getMerchantByUserId($this->userId());
        $shop = ScenicShopService::getInstance()->getFirstShop($merchant->id);

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
            $shop = ScenicShopService::getInstance()->getFirstShop($merchant->id);
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
