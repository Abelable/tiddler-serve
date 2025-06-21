<?php

namespace App\Services;

use App\Models\CartGoods;
use App\Models\Coupon;
use App\Models\ShopIncome;
use Illuminate\Support\Carbon;

class ShopIncomeService extends BaseService
{
    public function createIncome($shopId, $orderId, CartGoods $cartGoods, Coupon $coupon = null)
    {
        $couponDenomination = 0;
        if (!is_null($coupon) && $coupon->goods_id == $cartGoods->goods_id) {
            $couponDenomination = $coupon->denomination;
        }
        $totalPrice = bcmul($cartGoods->price, $cartGoods->number, 2);
        $paymentAmount = bcsub($totalPrice, $couponDenomination, 2);

        $salesCommissionRate = bcdiv($cartGoods->sales_commission_rate, 100, 4);
        $incomeRate = bcsub('1', $salesCommissionRate, 4);
        $incomeAmount = bcmul($totalPrice, $incomeRate, 2);

        $income = ShopIncome::new();
        $income->shop_id = $shopId;
        $income->order_id = $orderId;
        $income->goods_id = $cartGoods->goods_id;
        $income->refund_status = $cartGoods->refund_status;
        $income->payment_amount = $paymentAmount;
        $income->sales_commission_rate = $salesCommissionRate;
        $income->income_amount = $incomeAmount;
        $income->save();

        return $income;
    }

    public function getShopIncomeListByTimeType($shopId, $timeType, array $statusList, $columns = ['*'])
    {
        return $this->getShopIncomeQueryByTimeType($shopId, $timeType)->whereIn('status', $statusList)->get($columns);
    }

    public function getShopIncomeQueryByTimeType($shopId, $timeType, $startTime = null)
    {
        $query = ShopIncome::query()->where('shop_id', $shopId);

        switch ($timeType) {
            case 1:
                $query = $query->whereDate('created_at', Carbon::today());
                break;
            case 2:
                $query = $query->whereDate('created_at', Carbon::yesterday());
                break;
            case 3:
                $query = $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()]);
                break;
            case 4:
                $query = $query->whereBetween('created_at', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()]);
                break;
            case 5:
                $query = $query->whereBetween('created_at', [Carbon::now()->subMonths(2)->startOfMonth(), Carbon::now()->subMonths(2)->endOfMonth()]);
                break;
            case 6:
                $query = $query->whereBetween('created_at', [Carbon::now()->subMonths(2)->startOfMonth(), Carbon::now()]);
                break;
            case 7:
                $query = $query->whereBetween('created_at', [Carbon::parse($startTime), Carbon::now()]);
                break;
        }
        return $query;
    }

    public function getShopIncomeSum($shopId, $statusList)
    {
        return $this->getShopIncomeQuery($shopId, $statusList)->sum('income_amount');
    }

    public function getShopIncomeQuery($shopId, array $statusList)
    {
        return ShopIncome::query()->where('shop_id', $shopId)->whereIn('status', $statusList);
    }
}
