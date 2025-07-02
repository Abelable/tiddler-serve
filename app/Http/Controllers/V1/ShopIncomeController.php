<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ShopIncome;
use App\Services\GoodsService;
use App\Services\ProductHistoryService;
use App\Services\ShopIncomeService;
use App\Services\OrderGoodsService;
use App\Services\OrderService;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Carbon;

class ShopIncomeController extends Controller
{
    public function dataOverview()
    {
        $shopId = $this->verifyRequiredId('shopId');

        $totalIncome = ShopIncomeService::getInstance()->getShopIncomeSum($shopId, [1, 2, 3, 4]);

        $todayOrderQuery = OrderService::getInstance()->getShopDateQuery($shopId);
        $todaySalesVolume = (clone $todayOrderQuery)->sum('payment_amount');
        $todayOrderCount = (clone $todayOrderQuery)->count();

        $yesterdayOrderQuery = OrderService::getInstance()->getShopDateQuery($shopId, 'yesterday');
        $yesterdaySalesVolume = (clone $yesterdayOrderQuery)->sum('payment_amount');
        $yesterdayOrderCount = (clone $yesterdayOrderQuery)->count();


        $goodsIds = GoodsService::getInstance()->getShopGoodsList($shopId, [1])->pluck('id')->toArray();
        $todayVisitorCount = ProductHistoryService::getInstance()
            ->getHistoryDateCount(ProductType::GOODS, $goodsIds);
        $yesterdayVisitorCount = ProductHistoryService::getInstance()
            ->getHistoryDateCount(ProductType::GOODS, $goodsIds, 'yesterday');

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

        $cashAmount = ShopIncomeService::getInstance()
            ->getShopIncomeQuery($shopId, [2])
            ->whereMonth('created_at', '!=', Carbon::now()->month)
            ->sum('income_amount');
        $pendingAmount = ShopIncomeService::getInstance()->getShopIncomeSum($shopId, [1]);
        $withdrawingAmount = ShopIncomeService::getInstance()->getShopIncomeSum($shopId, [3]);
        $settledAmount = ShopIncomeService::getInstance()->getShopIncomeSum($shopId, [4]);

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

        $query = ShopIncomeService::getInstance()->getShopIncomeQueryByTimeType($shopId, $timeType);

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

        $page = ShopIncomeService::getInstance()->getShopIncomePageByTimeType($shopId, $timeType, $statusList, $input);
        $incomeList = collect($page->items());

        $orderIds = $incomeList->pluck('order_id')->toArray();
        $goodsIds = $incomeList->pluck('goods_id')->toArray();
        $goodsMap = [];
        $goodsList = OrderGoodsService::getInstance()->getListByOrderIdsAndGoodsIds($orderIds, $goodsIds);
        foreach ($goodsList as $goods) {
            $goodsMap[$goods->order_id][$goods->goods_id] = [
                'id' => $goods->goods_id,
                'cover' => $goods->cover,
                'name' => $goods->name,
                'selected_sku_name' => $goods->selected_sku_name,
                'price' => $goods->price,
                'number' => $goods->number,
            ];
        }

        $list = $incomeList->map(function (ShopIncome $income) use ($goodsMap) {
            $goodsInfo = $goodsMap[$income->order_id][$income->goods_id] ?? null;
            $income['goodsInfo'] = $goodsInfo;
            unset($income['goods_id']);
            return $income;
        });

        return $this->success($this->paginate($page, $list));
    }
}
