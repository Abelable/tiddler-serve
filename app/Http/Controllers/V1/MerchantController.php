<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Mall\Goods\MerchantService;
use App\Services\Mall\Goods\ShopDepositService;
use App\Services\Mall\Goods\ShopService;
use App\Services\SystemTodoService;
use App\Services\Task\UserTaskService;
use App\Utils\CodeResponse;
use App\Utils\Enums\TodoEnums;
use App\Utils\Inputs\MerchantInput;
use Illuminate\Support\Facades\DB;

class MerchantController extends Controller
{
    public function settleIn()
    {
        /** @var MerchantInput $input */
        $input = MerchantInput::new();

        $inviterId = $this->verifyId('inviterId');
        $taskId = $this->verifyId('taskId');

        $merchant = MerchantService::getInstance()->getMerchantByUserId($this->userId());
        if (!is_null($merchant)) {
            if ($merchant->status != 3) {
                return $this->fail(CodeResponse::DATA_EXISTED, '您已提交店铺申请，请勿重复提交');
            }
            DB::transaction(function () use ($merchant, $input) {
                $merchant->status = 0;
                $merchant->failure_reason = '';
                MerchantService::getInstance()->updateMerchant($merchant, $input);

                // todo 商家入驻通知
                SystemTodoService::getInstance()->createTodo(TodoEnums::GOODS_MERCHANT_NOTICE, [$merchant->id]);
            });
        } else {
            DB::transaction(function () use ($taskId, $inviterId, $input) {
                $merchant = MerchantService::getInstance()->createMerchant($input, $this->userId());
                $shop = ShopService::getInstance()->createShop($this->userId(), $merchant->id, $input);
                ShopDepositService::getInstance()->createShopDeposit($shop->id);

                // 邀请商家入驻活动
                if ($inviterId) {
                    $userTask = UserTaskService::getInstance()->getUserTaskByStatus($inviterId, $taskId, [1]);
                    if (!is_null($userTask)) {
                        $userTask->step = 1;
                        $userTask->merchant_id = $merchant->id;
                        $userTask->save();
                    }
                }

                SystemTodoService::getInstance()->createTodo(TodoEnums::GOODS_MERCHANT_NOTICE, [$merchant->id]);
                // todo 商家入驻通知
            });
        }

        return $this->success();
    }

    public function status()
    {
        $merchant = MerchantService::getInstance()->getMerchantByUserId($this->userId());
        $shop = ShopService::getInstance()->getFirstShop($merchant->id);

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
        $merchant = MerchantService::getInstance()->getMerchantByUserId($this->userId());
        if (!is_null($merchant)) {
            $shop = ShopService::getInstance()->getFirstShop($merchant->id);
            $merchant['shopCategoryIds'] = array_map('intval', json_decode($shop->category_ids));
            $merchant['deposit'] = $shop->deposit;
            $merchant['shopBg'] = $shop->bg;
            $merchant['shopLogo'] = $shop->logo;
            $merchant['shopName'] = $shop->name;
        }
        return $this->success($merchant);
    }

    public function delete()
    {
        $merchant = MerchantService::getInstance()->getMerchantByUserId($this->userId());
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '商家信息不存在');
        }
        $merchant->delete();
        return $this->success();
    }
}
