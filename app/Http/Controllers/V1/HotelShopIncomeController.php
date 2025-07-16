<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\HotelShopIncome;
use App\Models\ScenicShopIncome;
use App\Services\HotelOrderRoomService;
use App\Services\ProductHistoryService;
use App\Services\ScenicOrderService;
use App\Services\ScenicOrderTicketService;
use App\Services\HotelShopIncomeService;
use App\Services\ShopScenicSpotService;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Carbon;

class HotelShopIncomeController extends Controller
{
    public function dataOverview()
    {
        $shopId = $this->verifyRequiredId('shopId');

        $totalIncome = HotelShopIncomeService::getInstance()->getShopIncomeSum($shopId, [1, 2, 3, 4]);

        $todayOrderQuery = ScenicOrderService::getInstance()->getShopDateQuery($shopId);
        $todaySalesVolume = (clone $todayOrderQuery)->sum('payment_amount');
        $todayOrderCount = (clone $todayOrderQuery)->count();

        $yesterdayOrderQuery = ScenicOrderService::getInstance()->getShopDateQuery($shopId, 'yesterday');
        $yesterdaySalesVolume = (clone $yesterdayOrderQuery)->sum('payment_amount');
        $yesterdayOrderCount = (clone $yesterdayOrderQuery)->count();

        $scenicIds = ShopScenicSpotService::getInstance()
            ->getScenicList($shopId, [1])->pluck('id')->toArray();
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

        $cashAmount = HotelShopIncomeService::getInstance()
            ->getShopIncomeQuery($shopId, [2])
            ->whereMonth('created_at', '!=', Carbon::now()->month)
            ->sum('income_amount');
        $pendingAmount = HotelShopIncomeService::getInstance()->getShopIncomeSum($shopId, [1]);
        $withdrawingAmount = HotelShopIncomeService::getInstance()->getShopIncomeSum($shopId, [3]);
        $settledAmount = HotelShopIncomeService::getInstance()->getShopIncomeSum($shopId, [4]);

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

        $query = HotelShopIncomeService::getInstance()->getShopIncomeQueryByTimeType($shopId, $timeType);

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

        $page = HotelShopIncomeService::getInstance()
            ->getShopIncomePageByTimeType($shopId, $timeType, $statusList, $input);
        $incomeList = collect($page->items());

        $orderIds = $incomeList->pluck('order_id')->toArray();
        $roomIds = $incomeList->pluck('room_id')->toArray();
        $roomMap = [];
        $roomList = HotelOrderRoomService::getInstance()->getListByOrderIdsAndRoomIds($orderIds, $roomIds);
        foreach ($roomList as $room) {
            $room->image_list = json_decode($room->image_list);
            $room->facility_list = json_decode($room->facility_list);
            $roomMap[$room->order_id][$room->room_id] = $room;
        }

        $list = $incomeList->map(function (HotelShopIncome $income) use ($roomMap) {
            $roomInfo = $roomMap[$income->order_id][$income->room_id] ?? null;
            $income['roomInfo'] = $roomInfo;
            unset($income['room_id']);
            return $income;
        });

        return $this->success($this->paginate($page, $list));
    }
}
