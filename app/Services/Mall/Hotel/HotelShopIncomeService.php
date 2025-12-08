<?php

namespace App\Services\Mall\Hotel;

use App\Models\Mall\Hotel\HotelRoom;
use App\Models\Mall\Hotel\HotelShopIncome;
use App\Services\BaseService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Carbon;

class HotelShopIncomeService extends BaseService
{
    public function createIncome($shopId, $orderId, $orderSn, HotelRoom $room, $paymentAmount)
    {
        $salesCommissionRate = bcdiv($room->sales_commission_rate, 100, 4);
        $incomeRate = bcsub('1', $salesCommissionRate, 4);
        $incomeAmount = bcmul($paymentAmount, $incomeRate, 2);

        $income = HotelShopIncome::new();
        $income->shop_id = $shopId;
        $income->order_id = $orderId;
        $income->order_sn = $orderSn;
        $income->room_id = $room->id;
        $income->payment_amount = $paymentAmount;
        $income->sales_commission_rate = $room->sales_commission_rate;
        $income->income_amount = $incomeAmount;
        $income->save();

        return $income;
    }

    public function getShopIncomePageByTimeType($shopId, $timeType, array $statusList, PageInput $input, $columns = ['*'])
    {
        return $this
            ->getShopIncomeQueryByTimeType($shopId, $timeType)
            ->whereIn('status', $statusList)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getShopIncomeQueryByTimeType($shopId, $timeType, $startTime = null)
    {
        $query = HotelShopIncome::query()->where('shop_id', $shopId);

        switch ($timeType) {
            case 1:
                $query = $query->whereDate('created_at', Carbon::today());
                break;
            case 2:
                $query = $query->whereDate('created_at', Carbon::yesterday());
                break;
            case 3:
                $query = $query
                    ->whereBetween('created_at', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()
                    ]);
                break;
            case 4:
                $query = $query
                    ->whereBetween('created_at', [
                        Carbon::now()->subMonth()->startOfMonth(),
                        Carbon::now()->subMonth()->endOfMonth()
                    ]);
                break;
            case 5:
                $query = $query
                    ->whereBetween('created_at', [
                        Carbon::now()->subMonths(2)->startOfMonth(),
                        Carbon::now()->subMonths(2)->endOfMonth()
                    ]);
                break;
            case 6:
                $query = $query
                    ->whereBetween('created_at', [
                        Carbon::now()->subMonths(2)->startOfMonth(),
                        Carbon::now()
                    ]);
                break;
            case 7:
                $query = $query
                    ->whereBetween('created_at', [Carbon::parse($startTime), Carbon::now()]);
                break;
        }
        return $query;
    }

    public function getShopIncomeSum($shopId, $statusList)
    {
        return $this->getShopIncomeQuery($shopId, $statusList)->sum('income_amount');
    }

    public function applyWithdrawal($shopId, $withdrawalId)
    {
        $incomeList = $this->getWithdrawingList($shopId);
        /** @var HotelShopIncome $income */
        foreach ($incomeList as $income) {
            $income->withdrawal_id = $withdrawalId;
            $income->status = 3;
            $income->save();
        }
    }

    public function finishWithdrawal($shopId, $withdrawalId)
    {
        $incomeList = $this->getWithdrawingList($shopId);
        /** @var HotelShopIncome $income */
        foreach ($incomeList as $income) {
            $income->withdrawal_id = $withdrawalId;
            $income->status = 4;
            $income->save();
        }
    }

    public function getWithdrawingList($shopId)
    {
        return $this
            ->getShopIncomeQuery($shopId, [2])
            ->whereMonth('created_at', '!=', Carbon::now()->month)
            ->get();
    }

    public function getShopIncomeQuery($shopId, array $statusList)
    {
        return HotelShopIncome::query()->where('shop_id', $shopId)->whereIn('status', $statusList);
    }

    public function updateListToPaidStatus(array $orderIds)
    {
        return HotelShopIncome::query()
            ->whereIn('order_id', $orderIds)
            ->where('status', 0)
            ->update(['status' => 1]);
    }

    public function updateListToConfirmStatus($orderIds)
    {
        $incomeList = $this->getPaidListByOrderIds($orderIds);
        return $incomeList->map(function (HotelShopIncome $income) {
            $income->status = 2;
            $income->save();
            return $income;
        });
    }

    public function updateIncomeToConfirmStatus($id)
    {
        $income = $this->getPaidIncomeById($id);
        if (is_null($income)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '收益记录不存在或已删除');
        }
        $income->status = 2;
        $income->save();
        return $income;
    }

    public function getPaidListByOrderIds(array $orderIds, $columns = ['*'])
    {
        return HotelShopIncome::query()
            ->whereIn('order_id', $orderIds)
            ->where('status', 1)
            ->get($columns);
    }

    public function getPaidIncomeById($id, $columns = ['*'])
    {
        return HotelShopIncome::query()->where('status', 1)->where('id', $id)->first($columns);
    }

    public function deleteListByOrderIds(array $orderIds, $status)
    {
        return HotelShopIncome::query()
            ->whereIn('order_id', $orderIds)
            ->where('status', $status)
            ->delete();
    }

    public function deleteIncome($orderId, $roomId, $status)
    {
        return HotelShopIncome::query()
            ->where('order_id', $orderId)
            ->where('ticket_id', $roomId)
            ->where('status', $status)
            ->delete();
    }
}
