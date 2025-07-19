<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Catering\CateringShopIncome;
use App\Services\Mall\Catering\CateringShopIncomeService;
use App\Services\MealTicketOrderService;
use App\Services\OrderMealTicketService;
use App\Services\OrderSetMealService;
use App\Services\ProductHistoryService;
use App\Services\SetMealOrderService;
use App\Services\ShopRestaurantService;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Carbon;

class CateringShopIncomeController extends Controller
{
    public function dataOverview()
    {
        $shopId = $this->verifyRequiredId('shopId');

        $totalIncome = CateringShopIncomeService::getInstance()->getShopIncomeSum($shopId, [1, 2, 3, 4]);

        $mealTicketTodayOrderQuery = MealTicketOrderService::getInstance()->getShopDateQuery($shopId);
        $mealTicketTodaySalesVolume = (clone $mealTicketTodayOrderQuery)->sum('payment_amount');
        $mealTicketTodayOrderCount = (clone $mealTicketTodayOrderQuery)->count();

        $mealTicketYesterdayOrderQuery = MealTicketOrderService::getInstance()
            ->getShopDateQuery($shopId, 'yesterday');
        $mealTicketYesterdaySalesVolume = (clone $mealTicketYesterdayOrderQuery)->sum('payment_amount');
        $mealTicketYesterdayOrderCount = (clone $mealTicketYesterdayOrderQuery)->count();

        $setMealTodayOrderQuery = SetMealOrderService::getInstance()->getShopDateQuery($shopId);
        $setMealTodaySalesVolume = (clone $setMealTodayOrderQuery)->sum('payment_amount');
        $setMealTodayOrderCount = (clone $setMealTodayOrderQuery)->count();

        $setMealYesterdayOrderQuery = SetMealOrderService::getInstance()
            ->getShopDateQuery($shopId, 'yesterday');
        $setMealYesterdaySalesVolume = (clone $setMealYesterdayOrderQuery)->sum('payment_amount');
        $setMealYesterdayOrderCount = (clone $setMealYesterdayOrderQuery)->count();

        $restaurantIds = ShopRestaurantService::getInstance()
            ->getRestaurantList($shopId, [1])->pluck('restaurant_id')->toArray();
        $todayVisitorCount = ProductHistoryService::getInstance()
            ->getHistoryDateCount(ProductType::RESTAURANT, $restaurantIds);
        $yesterdayVisitorCount = ProductHistoryService::getInstance()
            ->getHistoryDateCount(ProductType::RESTAURANT, $restaurantIds, 'yesterday');

        return $this->success([
            'totalIncome' => $totalIncome,
            'todaySalesVolume' => $mealTicketTodaySalesVolume + $setMealTodaySalesVolume,
            'todayOrderCount' => $mealTicketTodayOrderCount + $setMealTodayOrderCount,
            'todayVisitorCount' => $todayVisitorCount,
            'yesterdaySalesVolume' => $mealTicketYesterdaySalesVolume + $setMealYesterdaySalesVolume,
            'yesterdayOrderCount' => $mealTicketYesterdayOrderCount + $setMealYesterdayOrderCount,
            'yesterdayVisitorCount' => $yesterdayVisitorCount,
        ]);
    }

    public function sum()
    {
        $shopId = $this->verifyRequiredId('shopId');

        $cashAmount = CateringShopIncomeService::getInstance()
            ->getShopIncomeQuery($shopId, [2])
            ->whereMonth('created_at', '!=', Carbon::now()->month)
            ->sum('income_amount');
        $pendingAmount = CateringShopIncomeService::getInstance()->getShopIncomeSum($shopId, [1]);
        $withdrawingAmount = CateringShopIncomeService::getInstance()->getShopIncomeSum($shopId, [3]);
        $settledAmount = CateringShopIncomeService::getInstance()->getShopIncomeSum($shopId, [4]);

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

        $query = CateringShopIncomeService::getInstance()
            ->getShopIncomeQueryByTimeType($shopId, $timeType);

        $orderCount = (clone $query)
            ->whereIn('status', [1, 2, 3, 4])
            ->distinct('order_id')
            ->count('order_id');
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

        $page = CateringShopIncomeService::getInstance()
            ->getShopIncomePageByTimeType($shopId, $timeType, $statusList, $input);
        $incomeList = collect($page->items());

        $orderIdsByType = $incomeList->groupBy('product_type')->map(function ($group) {
            return $group->pluck('order_id')->unique()->toArray();
        });
        $productIdsByType = $incomeList->groupBy('product_type')->map(function ($group) {
            return $group->pluck('product_id')->unique()->toArray();
        });

        $mealTicketMap = [];
        $mealTicketList = OrderMealTicketService::getInstance()->getListByOrderIdsAndMealTicketIds(
            $orderIdsByType[ProductType::MEAL_TICKET] ?? [],
            $productIdsByType[ProductType::MEAL_TICKET] ?? []
        );
        foreach ($mealTicketList as $mealTicket) {
            $mealTicketMap[$mealTicket->order_id][$mealTicket->ticket_id] = [
                'id' => $mealTicket->ticket_id,
                'price' => $mealTicket->price,
                'number' => $mealTicket->number,
            ];
        }

        $setMealMap = [];
        $setMealList = OrderSetMealService::getInstance()->getListByOrderIdsAndSetMealIds(
            $orderIdsByType[ProductType::SET_MEAL] ?? [],
            $productIdsByType[ProductType::SET_MEAL] ?? []
        );
        foreach ($setMealList as $setMeal) {
            $setMealMap[$setMeal->order_id][$setMeal->set_meal_id] = [
                'id' => $setMeal->set_meal_id,
                'cover' => $setMeal->cover,
                'name' => $setMeal->name,
                'price' => $setMeal->price,
                'number' => $setMeal->number,
            ];
        }

        $list = $incomeList->map(function (CateringShopIncome $income) use ($mealTicketMap, $setMealMap) {
            $orderId = $income->order_id;
            $productId = $income->product_id;
            $product = $income->product_type == ProductType::MEAL_TICKET
                ? $mealTicketMap[$orderId][$productId]
                : $setMealMap[$orderId][$productId];

            $income['product'] = $product;
            unset($income['product_id']);

            return $income;
        });

        return $this->success($this->paginate($page, $list));
    }
}
