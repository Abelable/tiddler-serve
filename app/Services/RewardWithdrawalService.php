<?php

namespace App\Services;

use App\Models\TaskRewardWithdrawal;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\WithdrawalPageInput;
use App\Utils\Inputs\RewardWithdrawalInput;
use Illuminate\Support\Facades\DB;

class RewardWithdrawalService extends BaseService
{
    public function addWithdrawal($userId, RewardWithdrawalInput $input)
    {
        $withdrawal = TaskRewardWithdrawal::new();

        if ($input->path == 3) { // 提现至余额
            $taxFee = 0;
            $handlingFee = 0;
            $actualAmount = $input->amount;
            $withdrawal->status = 1;
        } else {
            $taxFee = bcmul($input->amount, 0.06, 2);
            $handlingFee = bcmul($input->amount, 0.006, 2);
            $actualAmount = bcsub(bcsub($input->amount, $taxFee, 2), $handlingFee, 2);
        }

        $withdrawal->task_id = $input->taskId;
        $withdrawal->user_id = $userId;
        $withdrawal->withdraw_amount = $input->amount;
        $withdrawal->tax_fee = $taxFee;
        $withdrawal->handling_fee = $handlingFee;
        $withdrawal->actual_amount = $actualAmount;
        $withdrawal->path = $input->path;
        if (!empty($input->remark)) {
            $withdrawal->remark = $input->remark;
        }
        $withdrawal->save();

        return $withdrawal;
    }

    public function getUserRecordList($userId, PageInput $input, $columns = ['*'])
    {
        return TaskRewardWithdrawal::query()
            ->where('user_id', $userId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getList(WithdrawalPageInput $input, $columns = ['*'])
    {
        $query = TaskRewardWithdrawal::query();
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        if (!is_null($input->scene)) {
            $query = $query->where('scene', $input->scene);
        }
        if (!is_null($input->path)) {
            $query = $query->where('path', $input->path);
        }
        if (!is_null($input->userId)) {
            $query = $query->where('user_id', $input->userId);
        }
        return $query
            ->orderByRaw("FIELD(status, 0) DESC")
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getRecordById($id, $columns = ['*'])
    {
        return TaskRewardWithdrawal::query()->find($id, $columns);
    }

    public function getUserApplication($userId, $scene, $columns = ['*'])
    {
        return TaskRewardWithdrawal::query()
            ->where('user_id', $userId)
            ->where('scene', $scene)
            ->where('status', 0)
            ->first($columns);
    }

    public function getCountByStatus($status)
    {
        return TaskRewardWithdrawal::query()->where('status', $status)->count();
    }

    public function getWithdrawSumListByUserIds(array $userIds)
    {
        return TaskRewardWithdrawal::query()
            ->where('status', 1)
            ->whereIn('user_id', $userIds)
            ->select('user_id', DB::raw('SUM(withdraw_amount) as sum'))
            ->groupBy('user_id')
            ->get();
    }
}
