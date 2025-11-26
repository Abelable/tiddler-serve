<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\HotelMerchantService;
use App\Services\HotelShopDepositPaymentLogService;
use App\Services\HotelShopDepositService;
use App\Services\HotelShopService;
use App\Services\SystemTodoService;
use App\Services\UserTaskService;
use App\Utils\CodeResponse;
use App\Utils\Enums\TodoEnums;
use App\Utils\Inputs\HotelMerchantInput;
use Illuminate\Support\Facades\DB;

class HotelMerchantController extends Controller
{
    public function settleIn()
    {
        /** @var HotelMerchantInput $input */
        $input = HotelMerchantInput::new();

        $inviterId = $this->verifyId('inviterId');
        $taskId = $this->verifyId('taskId');

        $merchant = HotelMerchantService::getInstance()->getMerchantByUserId($this->userId());

        if (!is_null($merchant)) {
            if ($merchant->status != 3) {
                return $this->fail(CodeResponse::DATA_EXISTED, '您已提交店铺申请，请勿重复提交');
            }

            DB::transaction(function () use ($merchant, $input) {
                $merchant->status = 0;
                $merchant->failure_reason = '';
                HotelMerchantService::getInstance()->updateMerchant($merchant, $input);

                // todo 商家入驻通知
                SystemTodoService::getInstance()->createTodo(TodoEnums::MERCHANT_NOTICE, [$merchant->id]);
            });
        } else {
            DB::transaction(function () use ($taskId, $inviterId, $input) {
                $merchant = HotelMerchantService::getInstance()->createMerchant($input, $this->userId());
                $shop = HotelShopService::getInstance()->createShop($this->userId(), $merchant->id, $input);
                HotelShopDepositPaymentLogService::getInstance()
                    ->createLog($this->userId(), $merchant->id, $shop->id, $shop->deposit);
                HotelShopDepositService::getInstance()->createShopDeposit($shop->id);

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
                SystemTodoService::getInstance()->createTodo(TodoEnums::MERCHANT_NOTICE, [$merchant->id]);
            });
        }

        return $this->success();
    }

    public function status()
    {
        $merchant = HotelMerchantService::getInstance()->getMerchantByUserId($this->userId());
        // todo 目前一个商家对应一个店铺，暂时可以用商家id获取店铺，之后一个商家有多个店铺，需要传入店铺id
        $shop = HotelShopService::getInstance()->getShopByUserId($this->userId());

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
        $merchant = HotelMerchantService::getInstance()->getMerchantByUserId($this->userId());
        if (!is_null($merchant)) {
            $shop = HotelShopService::getInstance()->getShopByUserId($this->userId());
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
        $merchant = HotelMerchantService::getInstance()->getMerchantByUserId($this->userId());
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '酒店服务商信息不存在');
        }
        $merchant->delete();
        return $this->success();
    }
}
