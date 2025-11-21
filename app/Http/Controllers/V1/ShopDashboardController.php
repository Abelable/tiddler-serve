<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\AdminTodoService;
use App\Services\GoodsService;
use App\Services\OrderGoodsService;
use App\Services\OrderService;
use App\Services\ProductHistoryService;
use App\Services\ShopIncomeService;
use App\Utils\Enums\ProductType;

class ShopDashboardController extends Controller
{
    public function salesData()
    {
        $shopId = $this->verifyRequiredId('shopId');

        $totalSales = OrderService::getInstance()->shopSalesSum($shopId);
        $dailySalesList = OrderService::getInstance()->shopDailySalesList($shopId);
        $monthlySalesList = OrderService::getInstance()->shopMonthlySalesList($shopId);
        $dailyGrowthRate = OrderService::getInstance()->shopDailySalesGrowthRate($shopId);
        $weeklyGrowthRate = OrderService::getInstance()->shopWeeklySalesGrowthRate($shopId);

        return $this->success([
            'totalSales' => number_format($totalSales, 2),
            'dailySalesList' => $dailySalesList,
            'monthlySalesList' => $monthlySalesList,
            'dailyGrowthRate' => $dailyGrowthRate,
            'weeklyGrowthRate' => $weeklyGrowthRate,
        ]);
    }

    public function incomeData()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $statusList = [1, 2, 3, 4];

        $totalIncome = ShopIncomeService::getInstance()->getShopIncomeSum($shopId, $statusList);
        $dailyIncomeList = ShopIncomeService::getInstance()->dailyIncomeList($shopId, $statusList);
        $monthlyIncomeList = ShopIncomeService::getInstance()->monthlyIncomeList($shopId, $statusList);
        $dailyGrowthRate = ShopIncomeService::getInstance()->dailyIncomeGrowthRate($shopId, $statusList);
        $weeklyGrowthRate = ShopIncomeService::getInstance()->weeklyIncomeGrowthRate($shopId, $statusList);

        return $this->success([
            'totalIncome' => number_format($totalIncome, 2),
            'dailyIncomeList' => $dailyIncomeList,
            'monthlyIncomeList' => $monthlyIncomeList,
            'dailyGrowthRate' => $dailyGrowthRate,
            'weeklyGrowthRate' => $weeklyGrowthRate,
        ]);
    }

    public function orderCountData()
    {
        $shopId = $this->verifyRequiredId('shopId');

        $totalCount = OrderService::getInstance()->shopOrderCountSum($shopId);
        $dailyCountList = OrderService::getInstance()->shopDailyOrderCountList($shopId);
        $monthlyCountList = OrderService::getInstance()->shopMonthlyOrderCountList($shopId);
        $dailyGrowthRate = OrderService::getInstance()->shopDailyOrderCountGrowthRate($shopId);
        $weeklyGrowthRate = OrderService::getInstance()->shopWeeklyOrderCountGrowthRate($shopId);

        return $this->success([
            'totalCount' => $totalCount,
            'dailyCountList' => $dailyCountList,
            'monthlyCountList' => $monthlyCountList,
            'dailyGrowthRate' => $dailyGrowthRate,
            'weeklyGrowthRate' => $weeklyGrowthRate
        ]);
    }

    public function userCountData()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $goodsIds = GoodsService::getInstance()->getShopGoodsList($shopId, [1, 3])->pluck('id')->toArray();

        $totalCount = ProductHistoryService::getInstance()->countSum(ProductType::GOODS, $goodsIds);
        $dailyCountList = ProductHistoryService::getInstance()->dailyCountList(ProductType::GOODS, $goodsIds);
        $dailyGrowthRate = ProductHistoryService::getInstance()->dailyCountGrowthRate(ProductType::GOODS, $goodsIds);
        $weeklyGrowthRate = ProductHistoryService::getInstance()->weeklyCountGrowthRate(ProductType::GOODS, $goodsIds);

        return $this->success([
            'totalCount' => $totalCount,
            'dailyCountList' => $dailyCountList,
            'dailyGrowthRate' => $dailyGrowthRate,
            'weeklyGrowthRate' => $weeklyGrowthRate
        ]);
    }

    public function topGoodsList()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $startDate = $this->verifyRequiredString('startDate');
        $endDate = $this->verifyRequiredString('endDate');

        $topSalesList = OrderGoodsService::getInstance()->getShopTopSalesGoodsList($shopId, $startDate, $endDate);
        $topOrderCountList = OrderGoodsService::getInstance()->getShopTopOrderCountGoodsList($shopId, $startDate, $endDate);

        $topSalesGoodsIds = $topSalesList->pluck('goods_id')->toArray();
        $topOrderCountGoodsIds = $topOrderCountList->pluck('goods_id')->toArray();
        $goodsIds = array_unique(array_merge($topSalesGoodsIds, $topOrderCountGoodsIds));
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds)->keyBy('id');

        $topSalesGoodsList = $topSalesList->map(function ($item) use ($goodsList) {
            $goods = $goodsList->get($item->goods_id);
            return [
                'id' => $goods->id,
                'cover' => $goods->cover,
                'name' => $goods->name,
                'sum' => $item->sum,
            ];
        });

        $topOrderCountGoodsList = $topOrderCountList->map(function ($item) use ($goodsList) {
            $goods = $goodsList->get($item->goods_id);
            return [
                'id' => $goods->id,
                'cover' => $goods->cover,
                'name' => $goods->name,
                'count' => $item->count,
            ];
        });

        return $this->success([
            'topSalesGoodsList' => $topSalesGoodsList,
            'topOrderCountGoodsList' => $topOrderCountGoodsList
        ]);
    }

    public function todoList()
    {
        $todoList = AdminTodoService::getInstance()->getTodoList();
        return $this->success($todoList);
    }
}
