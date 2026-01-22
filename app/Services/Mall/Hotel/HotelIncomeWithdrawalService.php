<?php

namespace App\Services\Mall\Hotel;

use App\Models\Mall\Hotel\HotelShopIncomeWithdrawal;
use App\Services\BaseService;
use App\Utils\Inputs\IncomeWithdrawalInput;
use App\Utils\Inputs\IncomeWithdrawalPageInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

class HotelIncomeWithdrawalService extends BaseService
{
    public function addWithdrawal($shopId, $userId, $withdrawAmount, IncomeWithdrawalInput $input)
    {
        $withdrawal = HotelShopIncomeWithdrawal::new();

        // todo 千六手续费临时改为0
        $handlingFee = 0;
        //  $handlingFee = MathTool::bcRound(bcmul($withdrawAmount, '0.006', 10));

        $actualAmount = bcsub($withdrawAmount, $handlingFee, 2);

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

    public function getShopPage($shopId, PageInput $input, $columns = ['*'])
    {
        return HotelShopIncomeWithdrawal::query()
            ->where('shop_id', $shopId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getAdminPage(IncomeWithdrawalPageInput $input, $columns = ['*'])
    {
        $query = HotelShopIncomeWithdrawal::query();
        if (!is_null($input->merchantType)) {
            $query = $query->where('merchant_type', $input->merchantType);
        }
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
