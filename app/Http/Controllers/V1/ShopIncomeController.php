<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ShopIncome;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Services\ShopIncomeService;
use App\Services\OrderGoodsService;
use App\Services\OrderService;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Carbon;

class ShopIncomeController extends Controller
{
    public function incomeOrderList()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $timeType = $this->verifyRequiredInteger('timeType');
        $statusList = $this->verifyArray('statusList');
        /** @var PageInput $input */
        $input = PageInput::new();

        $incomeList = ShopIncomeService::getInstance()->getShopIncomeListByTimeType($shopId, $timeType, $statusList);
        $groupIncomeList = $incomeList->groupBy('order_id');
        $keyIncomeList = $incomeList->mapWithKeys(function ($income) {
            return [ $income->order_id . '_' . $income->goods_id => $income ];
        });
        $orderIds = $incomeList->pluck('order_id')->toArray();

        $goodsIds = $incomeList->pluck('goods_id')->toArray();
        $goodsColumns = ['order_id', 'goods_id', 'cover', 'name', 'selected_sku_name', 'price', 'number'];
        $groupGoodsList = OrderGoodsService::getInstance()->getListByGoodsIds($goodsIds, $goodsColumns)->groupBy('order_id');

        $page = OrderService::getInstance()->getOrderPageByIds($orderIds, $input);
        $list = collect($page->items())->map(function (Order $order) use ($groupGoodsList, $keyIncomeList, $groupIncomeList) {
            $orderIncomeList = $groupIncomeList->get($order->id);
            $incomeAmountSum = $orderIncomeList->sum('income_amount');
            /** @var ShopIncome $firstIncome */
            $firstIncome = $orderIncomeList->first();

            $orderGoodsList = $groupGoodsList->get($order->id);
            $orderGoodsList->map(function (OrderGoods $goods) use ($order, $keyIncomeList) {
                $incomeKey = $order->id . '_' . $goods->goods_id;
                /** @var ShopIncome $income */
                $income = $keyIncomeList->get($incomeKey);
                $goods['income'] = $income->income_amount;
                unset($goods->order_id);
                return $goods;
            });

            return [
                'id' => $order->id,
                'orderSn' => $order->order_sn,
                'status' => $firstIncome->status,
                'paymentAmount' => $order->payment_amount,
                'incomeAmount' => bcadd($incomeAmountSum, 0, 2) ,
                'goodsList' => $orderGoodsList,
                'createdAt' => $order->created_at
            ];
        });

        return $this->success($this->paginate($page, $list));
    }

    public function sum()
    {
        $shopId = $this->verifyRequiredId('shopId');

        $cashAmount = ShopIncomeService::getInstance()
            ->getShopIncomeQuery($shopId, [2])
            ->whereMonth('created_at', '!=', Carbon::now()->month)
            ->sum('income_amount');
        $pendingAmount = ShopIncomeService::getInstance()->getShopIncomeSum($shopId, [1]);
        $settledAmount = ShopIncomeService::getInstance()->getShopIncomeSum($shopId, [2, 3, 4]);
        return $this->success([
            'cashAmount' => $cashAmount,
            'pendingAmount' => $pendingAmount,
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
}
