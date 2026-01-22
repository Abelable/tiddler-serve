<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mall\CommissionWithdrawal;
use App\Services\BankCardService;
use App\Services\Mall\CommissionService;
use App\Services\Mall\CommissionWithdrawalService;
use App\Services\SystemTodoService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Enums\TodoEnums;
use App\Utils\Inputs\WithdrawalPageInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yansongda\LaravelPay\Facades\Pay;

class CommissionWithdrawalController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var WithdrawalPageInput $input */
        $input = WithdrawalPageInput::new();
        $page = CommissionWithdrawalService::getInstance()->getList($input);
        $recordList = collect($page->items());

        $userIds = $recordList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname'])->keyBy('id');

        $bankCardList = BankCardService::getInstance()
            ->getListByUserIds($userIds, ['user_id', 'code', 'name', 'bank_name'])
            ->keyBy('user_id');

        $list = $recordList->map(function (CommissionWithdrawal $withdrawal) use ($bankCardList, $userList) {
            $userInfo = $userList->get($withdrawal->user_id);
            $withdrawal['userInfo'] = $userInfo;
            if ($withdrawal->path == 2) {
                $bankCard = $bankCardList->get($withdrawal->user_id);
                unset($bankCard->user_id);
                $withdrawal['bankCardInfo'] = $bankCard;
            }
            unset($withdrawal->user_id);
            return $withdrawal;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $record = CommissionWithdrawalService::getInstance()->getRecordById($id);
        if (is_null($record)) {
            return $this->fail(CodeResponse::NOT_FOUND, '提现申请不存在');
        }
        return $this->success($record);
    }

    public function approved()
    {
        $id = $this->verifyRequiredId('id');
        $record = CommissionWithdrawalService::getInstance()->getRecordById($id);
        if (is_null($record)) {
            return $this->fail(CodeResponse::NOT_FOUND, '提现申请不存在');
        }

        $user = UserService::getInstance()->getUserById($record->user_id);

        // 校验提现金额
        $commissionSum = CommissionService::getInstance()->getCommissionSumByWithdrawalId($record->id);
        if (bccomp($commissionSum, $record->withdraw_amount, 2) != 0) {
            $errMsg = "用户（ID：{$record->user_id}）提现金额（{$record->withdraw_amount}）与实际可提现金额（{$commissionSum}）不一致，请检查";
            Log::error($errMsg);
            return $this->fail(CodeResponse::INVALID_OPERATION, $errMsg);
        }

        DB::transaction(function () use ($user, $record) {
            CommissionService::getInstance()->settleCommissionByWithdrawalId($record->id);
            $record->status = 1;
            $record->save();

            SystemTodoService::getInstance()->createTodo(TodoEnums::COMMISSION_WITHDRAWAL_NOTICE, $record->id);

            $target = $record->path == 1 ? '微信账号' : '银行账号';
            $noticeContent = '您申请提现的¥' . $record->actual_amount . '佣金，已成功转入您的' . $target . '，请注意查收';
            // todo 消息通知
            // NotificationService::getInstance()
            //  ->addNotification(NotificationEnums::WITHDRAWAL_NOTICE, '佣金提现成功通知', $noticeContent, $record->user_id);

            if ($record->path == 1) {
                // todo 微信转账
                $params = [
                    'partner_trade_no' => time(),
                    'openid' => $user->openid,
                    'check_name' => 'NO_CHECK',
                    'amount' => bcmul($record->actual_amount, 100),
                    'desc' => '代言奖励提现',
                ];
                $result = Pay::wechat()->transfer($params);
                Log::info('commission_wx_transfer', $result->toArray());
            }
        });

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');
        $record = CommissionWithdrawalService::getInstance()->getRecordById($id);
        if (is_null($record)) {
            return $this->fail(CodeResponse::NOT_FOUND, '提现申请不存在');
        }

        $user = UserService::getInstance()->getUserById($record->user_id);
        DB::transaction(function () use ($reason, $user, $record) {
            CommissionService::getInstance()->restoreCommissionByWithdrawalId($record->id);

            $record->status = 2;
            $record->failure_reason = $reason;
            $record->save();

            SystemTodoService::getInstance()->deleteTodo(TodoEnums::COMMISSION_WITHDRAWAL_NOTICE, $record->id);
            // todo 消息通知
            // NotificationService::getInstance()->addNotification(NotificationEnums::WITHDRAWAL_NOTICE, '佣金提现失败通知', $reason, $record->user_id);
        });

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $record = CommissionWithdrawalService::getInstance()->getRecordById($id);
        if (is_null($record)) {
            return $this->fail(CodeResponse::NOT_FOUND, '提现申请不存在');
        }
        $record->delete();
        return $this->success();
    }

    public function getPendingCount()
    {
        $count = CommissionWithdrawalService::getInstance()->getCountByStatus(0);
        return $this->success($count);
    }
}
