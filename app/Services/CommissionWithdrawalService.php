<?php

namespace App\Services;

use App\Models\CommissionWithdrawal;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\WithdrawalPageInput;
use App\Utils\Inputs\CommissionWithdrawalInput;
use Illuminate\Support\Facades\DB;

class CommissionWithdrawalService extends BaseService
{
    public function addWithdrawal($userId, $withdrawAmount, CommissionWithdrawalInput $input)
    {
        $withdrawal = CommissionWithdrawal::new();

        if ($input->path == 3) { // 提现至余额
            $taxFee = 0;
            $handlingFee = 0;
            $actualAmount = $withdrawAmount;
            $withdrawal->status = 1;
        } else {
            $taxFee = $input->scene == 1 ? 0 : bcmul($withdrawAmount, 0.06, 2);
            $handlingFee = bcmul($withdrawAmount, 0.006, 2);
            $actualAmount = bcsub(bcsub($withdrawAmount, $taxFee, 2), $handlingFee, 2);
        }

        $withdrawal->user_id = $userId;
        $withdrawal->scene = $input->scene;
        $withdrawal->withdraw_amount = $withdrawAmount;
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
        return CommissionWithdrawal::query()
            ->where('user_id', $userId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getList(WithdrawalPageInput $input, $columns = ['*'])
    {
        $query = CommissionWithdrawal::query();
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
        return CommissionWithdrawal::query()->find($id, $columns);
    }

    public function getUserApplication($userId, $scene, $columns = ['*'])
    {
        return CommissionWithdrawal::query()
            ->where('user_id', $userId)
            ->where('scene', $scene)
            ->where('status', 0)
            ->first($columns);
    }

    public function getCountByStatus($status)
    {
        return CommissionWithdrawal::query()->where('status', $status)->count();
    }

    public function getWithdrawSumListByUserIds(array $userIds)
    {
        return CommissionWithdrawal::query()
            ->where('status', 1)
            ->whereIn('user_id', $userIds)
            ->select('user_id', DB::raw('SUM(withdraw_amount) as sum'))
            ->groupBy('user_id')
            ->get();
    }
}
