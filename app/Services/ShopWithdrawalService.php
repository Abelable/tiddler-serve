<?php

namespace App\Services;

use App\Models\ShopIncomeWithdrawal;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\ShopWithdrawalPageInput;
use App\Utils\Inputs\IncomeWithdrawalInput;
use Illuminate\Support\Facades\DB;

class ShopWithdrawalService extends BaseService
{
    public function addWithdrawal($merchantType, $shopId, $userId, $withdrawAmount, IncomeWithdrawalInput $input)
    {
        $withdrawal = ShopIncomeWithdrawal::new();

        if ($input->path == 3) { // 提现至余额
            $handlingFee = 0;
            $actualAmount = $withdrawAmount;
            $withdrawal->status = 1;
        } else {
            $handlingFee = bcmul($withdrawAmount, '0.006', 2);
            $actualAmount = bcsub($withdrawAmount, $handlingFee, 2);
        }

        $withdrawal->merchant_type = $merchantType;
        $withdrawal->shop_id = $shopId;
        $withdrawal->user_id = $userId;
        $withdrawal->withdraw_amount = $withdrawAmount;
        $withdrawal->handling_fee = $handlingFee;
        $withdrawal->actual_amount = $actualAmount;
        $withdrawal->path = $input->path;
        if (!empty($input->remark)) {
            $withdrawal->remark = $input->remark;
        }
        $withdrawal->save();

        return $withdrawal;
    }

    public function getShopPage($merchantType, $shopId, PageInput $input, $columns = ['*'])
    {
        return ShopIncomeWithdrawal::query()
            ->where('merchant_type', $merchantType)
            ->where('shop_id', $shopId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getAdminPage(ShopWithdrawalPageInput $input, $columns = ['*'])
    {
        $query = ShopIncomeWithdrawal::query();
        if (!is_null($input->shopId)) {
            $query = $query->where('shop_id', $input->shopId);
        }
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        if (!is_null($input->path)) {
            $query = $query->where('path', $input->path);
        }
        return $query
            ->orderByRaw("FIELD(status, 0) DESC")
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getRecordById($id, $columns = ['*'])
    {
        return ShopIncomeWithdrawal::query()->find($id, $columns);
    }

    public function getUserApplication($userId, $scene, $columns = ['*'])
    {
        return ShopIncomeWithdrawal::query()
            ->where('user_id', $userId)
            ->where('scene', $scene)
            ->where('status', 0)
            ->first($columns);
    }

    public function getCountByStatus($status)
    {
        return ShopIncomeWithdrawal::query()->where('status', $status)->count();
    }

    public function getWithdrawSumListByUserIds(array $userIds)
    {
        return ShopIncomeWithdrawal::query()
            ->where('status', 1)
            ->whereIn('user_id', $userIds)
            ->select('user_id', DB::raw('SUM(withdraw_amount) as sum'))
            ->groupBy('user_id')
            ->get();
    }
}
