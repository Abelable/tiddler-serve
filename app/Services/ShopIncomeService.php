<?php

namespace App\Services;

use App\Models\CartGoods;
use App\Models\Coupon;
use App\Models\ShopIncome;

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
}
