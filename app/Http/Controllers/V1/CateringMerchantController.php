<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Mall\Catering\CateringMerchantService;
use App\Services\Mall\Catering\CateringShopDepositService;
use App\Services\Mall\Catering\CateringShopService;
use App\Services\SystemTodoService;
use App\Services\Task\UserTaskService;
use App\Utils\CodeResponse;
use App\Utils\Enums\TodoEnums;
use App\Utils\Inputs\CateringMerchantInput;
use Illuminate\Support\Facades\DB;

class CateringMerchantController extends Controller
{
    public function settleIn()
    {
        /** @var CateringMerchantInput $input */
        $input = CateringMerchantInput::new();

        $inviterId = $this->verifyId('inviterId');
        $taskId = $this->verifyId('taskId');

        $merchant = CateringMerchantService::getInstance()->getMerchantByUserId($this->userId());

        if (!is_null($merchant)) {
            if ($merchant->status != 3) {
                return $this->fail(CodeResponse::DATA_EXISTED, '您已提交店铺申请，请勿重复提交');
            }

            DB::transaction(function () use ($merchant, $input) {
                $merchant->status = 0;
                $merchant->failure_reason = '';
                CateringMerchantService::getInstance()->updateMerchant($merchant, $input);

                // todo 商家入驻通知
                SystemTodoService::getInstance()->createTodo(TodoEnums::CATERING_MERCHANT_NOTICE, [$merchant->id]);
            });

        } else {
            DB::transaction(function () use ($taskId, $inviterId, $input) {
                $merchant = CateringMerchantService::getInstance()->createMerchant($input, $this->userId());
                $shop = CateringShopService::getInstance()->createShop($this->userId(), $merchant->id, $input);
                CateringShopDepositService::getInstance()->createShopDeposit($shop->id);

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
                SystemTodoService::getInstance()->createTodo(TodoEnums::CATERING_MERCHANT_NOTICE, [$merchant->id]);
            });
        }

        return $this->success();
    }

    public function status()
    {
        $merchant = CateringMerchantService::getInstance()->getMerchantByUserId($this->userId());
        $shop = CateringShopService::getInstance()->getFirstShop($merchant->id);

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
        $merchant = CateringMerchantService::getInstance()->getMerchantByUserId($this->userId());
        if (!is_null($merchant)) {
            $shop = CateringShopService::getInstance()->getFirstShop($merchant->id);
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
        $merchant = CateringMerchantService::getInstance()->getMerchantByUserId($this->userId());
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '餐饮服务商信息不存在');
        }
        $merchant->delete();
        return $this->success();
    }
}
