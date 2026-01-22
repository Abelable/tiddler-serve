<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mall\Goods\Shop;
use App\Models\Mall\Goods\ShopIncomeWithdrawal;
use App\Services\Mall\Goods\GoodsShopIncomeService;
use App\Services\Mall\Goods\GoodsShopIncomeWithdrawalService;
use App\Services\Mall\Goods\MerchantService;
use App\Services\Mall\Goods\ShopService;
use App\Services\SystemTodoService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Enums\TodoEnums;
use App\Utils\Inputs\IncomeWithdrawalPageInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoodsShopIncomeWithdrawalController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var IncomeWithdrawalPageInput $input */
        $input = IncomeWithdrawalPageInput::new();
        $page = GoodsShopIncomeWithdrawalService::getInstance()->getAdminPage($input);
        $recordList = collect($page->items());

        $userIds = $recordList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname'])->keyBy('id');

        $shopIds = $recordList->pluck('shop_id')->toArray();
        $shopList = ShopService::getInstance()->getShopListByIds($shopIds);

        $merchantIds = $shopList->pluck('merchant_id')->toArray();
        $merchantList = MerchantService::getInstance()->getMerchantListByIds($merchantIds)->keyBy('id');

        $list = $recordList->map(function (ShopIncomeWithdrawal $withdrawal) use ($merchantList, $shopList, $userList) {
            $userInfo = $userList->get($withdrawal->user_id);
            $withdrawal['userInfo'] = $userInfo;

            /** @var Shop $shopInfo */
            $shopInfo = $shopList->keyBy('id')->get($withdrawal->shop_id);
            $withdrawal['shopInfo'] = $shopInfo;

            $merchantInfo = $merchantList->get($shopInfo->merchant_id);
            $withdrawal['merchantInfo'] = $merchantInfo;

            return $withdrawal;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $record = GoodsShopIncomeWithdrawalService::getInstance()->getRecordById($id);
        if (is_null($record)) {
            return $this->fail(CodeResponse::NOT_FOUND, '提现申请不存在');
        }
        return $this->success($record);
    }

    public function approved()
    {
        $id = $this->verifyRequiredId('id');
        $record = GoodsShopIncomeWithdrawalService::getInstance()->getRecordById($id);
        if (is_null($record)) {
            return $this->fail(CodeResponse::NOT_FOUND, '提现申请不存在');
        }

        // 校验提现金额
        $incomeSum = GoodsShopIncomeService::getInstance()->getIncomeSumByWithdrawalId($record->id);
        if (bccomp($incomeSum, $record->withdraw_amount, 2) != 0) {
            $errMsg = "用户（ID：{$record->user_id}）提现店铺（ID：{$record->shop_id}）收益金额（{$record->withdraw_amount}）与实际可提现金额（{$incomeSum}）不一致，请检查";
            Log::error($errMsg);
            return $this->fail(CodeResponse::INVALID_OPERATION, $errMsg);
        }

        DB::transaction(function () use ($record) {
            GoodsShopIncomeService::getInstance()->settleIncomeByWithdrawalId($record->id);

            $record->status = 1;
            $record->save();

            SystemTodoService::getInstance()->createTodo(TodoEnums::GOODS_INCOME_WITHDRAWAL_NOTICE, $record->id);

            // todo 消息通知
            // $noticeContent = '您申请提现的¥' . $record->actual_amount . '店铺收益，已成功转入您的银行账号请注意查收';
            // NotificationService::getInstance()->addNotification(NotificationEnums::WITHDRAWAL_NOTICE, '佣金提现成功通知', $noticeContent, $record->user_id);
        });

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $record = GoodsShopIncomeWithdrawalService::getInstance()->getRecordById($id);
        if (is_null($record)) {
            return $this->fail(CodeResponse::NOT_FOUND, '提现申请不存在');
        }

        DB::transaction(function () use ($reason, $record) {
            GoodsShopIncomeService::getInstance()->restoreCommissionByWithdrawalId($record->id);

            $record->status = 2;
            $record->failure_reason = $reason;
            $record->save();

            SystemTodoService::getInstance()->deleteTodo(TodoEnums::GOODS_INCOME_WITHDRAWAL_NOTICE, $record->id);
            // todo 消息通知
            // NotificationService::getInstance()->addNotification(NotificationEnums::WITHDRAWAL_NOTICE, '佣金提现失败通知', $reason, $record->user_id);
        });

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $record = GoodsShopIncomeWithdrawalService::getInstance()->getRecordById($id);
        if (is_null($record)) {
            return $this->fail(CodeResponse::NOT_FOUND, '提现申请不存在');
        }
        $record->delete();
        return $this->success();
    }

    public function getPendingCount()
    {
        $count = GoodsShopIncomeWithdrawalService::getInstance()->getCountByStatus(0);
        return $this->success($count);
    }
}
