<?php

namespace App\Services;

use App\Models\HotelShopIncomeWithdrawal;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\ShopWithdrawalPageInput;
use App\Utils\Inputs\IncomeWithdrawalInput;
use Illuminate\Support\Facades\DB;

class HotelShopWithdrawalService extends BaseService
{
    public function addWithdrawal($userId, $shopId, $withdrawAmount, IncomeWithdrawalInput $input)
    {
        $withdrawal = HotelShopIncomeWithdrawal::new();

        if ($input->path == 3) { // 提现至余额
            $taxFee = 0;
            $handlingFee = 0;
            $actualAmount = $withdrawAmount;
            $withdrawal->status = 1;
        } else {
            $taxFee = bcmul($withdrawAmount, '0.06', 2);
            $handlingFee = '1.00';
            $actualAmount = bcsub(bcsub($withdrawAmount, $taxFee, 2), $handlingFee, 2);
        }

        $withdrawal->user_id = $userId;
        $withdrawal->shop_id = $shopId;
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

    public function getShopPage($shopId, PageInput $input, $columns = ['*'])
    {
        return HotelShopIncomeWithdrawal::query()
            ->where('shop_id', $shopId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getAdminPage(ShopWithdrawalPageInput $input, $columns = ['*'])
    {
        $query = HotelShopIncomeWithdrawal::query();
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
        return HotelShopIncomeWithdrawal::query()->find($id, $columns);
    }

    public function getUserApplication($userId, $scene, $columns = ['*'])
    {
        return HotelShopIncomeWithdrawal::query()
            ->where('user_id', $userId)
            ->where('scene', $scene)
            ->where('status', 0)
            ->first($columns);
    }

    public function getCountByStatus($status)
    {
        return HotelShopIncomeWithdrawal::query()->where('status', $status)->count();
    }

    public function getWithdrawSumListByUserIds(array $userIds)
    {
        return HotelShopIncomeWithdrawal::query()
            ->where('status', 1)
            ->whereIn('user_id', $userIds)
            ->select('user_id', DB::raw('SUM(withdraw_amount) as sum'))
            ->groupBy('user_id')
            ->get();
    }
}
