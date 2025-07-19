<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ScenicShopIncome;
use App\Services\ProductHistoryService;
use App\Services\ScenicOrderService;
use App\Services\ScenicOrderTicketService;
use App\Services\ScenicShopIncomeService;
use App\Services\ShopScenicService;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Carbon;

class ScenicShopIncomeController extends Controller
{
    public function dataOverview()
    {
        $shopId = $this->verifyRequiredId('shopId');

        $totalIncome = ScenicShopIncomeService::getInstance()->getShopIncomeSum($shopId, [1, 2, 3, 4]);

        $todayOrderQuery = ScenicOrderService::getInstance()->getShopDateQuery($shopId);
        $todaySalesVolume = (clone $todayOrderQuery)->sum('payment_amount');
        $todayOrderCount = (clone $todayOrderQuery)->count();

        $yesterdayOrderQuery = ScenicOrderService::getInstance()->getShopDateQuery($shopId, 'yesterday');
        $yesterdaySalesVolume = (clone $yesterdayOrderQuery)->sum('payment_amount');
        $yesterdayOrderCount = (clone $yesterdayOrderQuery)->count();

        $scenicIds = ShopScenicService::getInstance()
            ->getScenicList($shopId, [1])->pluck('scenic_id')->toArray();
        $todayVisitorCount = ProductHistoryService::getInstance()
            ->getHistoryDateCount(ProductType::SCENIC, $scenicIds);
        $yesterdayVisitorCount = ProductHistoryService::getInstance()
            ->getHistoryDateCount(ProductType::SCENIC, $scenicIds, 'yesterday');

        return $this->success([
            'totalIncome' => $totalIncome,
            'todaySalesVolume' => $todaySalesVolume,
            'todayOrderCount' => $todayOrderCount,
            'todayVisitorCount' => $todayVisitorCount,
            'yesterdaySalesVolume' => $yesterdaySalesVolume,
            'yesterdayOrderCount' => $yesterdayOrderCount,
            'yesterdayVisitorCount' => $yesterdayVisitorCount,
        ]);
    }

    public function sum()
    {
        $shopId = $this->verifyRequiredId('shopId');

        $cashAmount = ScenicShopIncomeService::getInstance()
            ->getShopIncomeQuery($shopId, [2])
            ->whereMonth('created_at', '!=', Carbon::now()->month)
            ->sum('income_amount');
        $pendingAmount = ScenicShopIncomeService::getInstance()->getShopIncomeSum($shopId, [1]);
        $withdrawingAmount = ScenicShopIncomeService::getInstance()->getShopIncomeSum($shopId, [3]);
        $settledAmount = ScenicShopIncomeService::getInstance()->getShopIncomeSum($shopId, [4]);

        return $this->success([
            'cashAmount' => $cashAmount,
            'pendingAmount' => $pendingAmount,
            'withdrawingAmount' => $withdrawingAmount,
            'settledAmount' => $settledAmount
        ]);
    }

    public function timeData()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $timeType = $this->verifyRequiredInteger('timeType');

        $query = ScenicShopIncomeService::getInstance()->getShopIncomeQueryByTimeType($shopId, $timeType);

        $orderCount = (clone $query)->whereIn('status', [1, 2, 3, 4])->distinct('order_id')->count('order_id');
        $salesVolume = (clone $query)->whereIn('status', [1, 2, 3, 4])->sum('payment_amount');
        $pendingAmount = (clone $query)->where('status', 1)->sum('income_amount');
        $settledAmount = (clone $query)->whereIn('status', [2, 3, 4])->sum('income_amount');

        return $this->success([
            'orderCount' => $orderCount,
            'salesVolume' => $salesVolume,
            'pendingAmount' => $pendingAmount,
            'settledAmount' => $settledAmount
        ]);
    }

    public function incomeOrderList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $shopId = $this->verifyRequiredId('shopId');
        $timeType = $this->verifyRequiredInteger('timeType');
        $statusList = $this->verifyArray('statusList');

        $page = ScenicShopIncomeService::getInstance()
            ->getShopIncomePageByTimeType($shopId, $timeType, $statusList, $input);
        $incomeList = collect($page->items());

        $orderIds = $incomeList->pluck('order_id')->toArray();
        $ticketIds = $incomeList->pluck('ticket_id')->toArray();
        $ticketMap = [];
        $ticketList = ScenicOrderTicketService::getInstance()
            ->getListByOrderIdsAndTicketIds($orderIds, $ticketIds);
        foreach ($ticketList as $ticket) {
            $ticketMap[$ticket->order_id][$ticket->ticket_id] = [
                'id' => $ticket->ticket_id,
                'name' => $ticket->name,
                'categoryName' => $ticket->category_name,
                'price' => $ticket->price,
                'number' => $ticket->number,
                'scenicList' => json_decode($ticket->scenic_list),
                'validityTime' => $ticket->validity_time,
                'selectedDateTimestamp' => $ticket->selected_date_timestamp,
                'effectiveTime' => $ticket->effective_time,
                'refundStatus' => $ticket->refund_status,
                'needExchange' => $ticket->need_exchange
            ];
        }

        $list = $incomeList->map(function (ScenicShopIncome $income) use ($ticketMap) {
            $ticketInfo = $ticketMap[$income->order_id][$income->ticket_id] ?? null;
            $income['ticketInfo'] = $ticketInfo;
            unset($income['ticket_id']);
            return $income;
        });

        return $this->success($this->paginate($page, $list));
    }
}
