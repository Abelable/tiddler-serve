<?php

namespace App\Services;

use App\Jobs\ShopIncomeConfirmJob;
use App\Models\Address;
use App\Models\CartGoods;
use App\Models\Coupon;
use App\Models\ShopIncome;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ShopIncomeService extends BaseService
{
    public function createIncome(
        $shopId,
        $orderId,
        $orderSn,
        $cartGoodsList,
        Coupon $coupon,
        $deliveryMode,
        $freightTemplateList = null,
        Address $address = null
    )
    {
        /** @var CartGoods $cartGoods */
        foreach ($cartGoodsList as $cartGoods) {
            // 总价：单价 * 数量
            $totalPrice = bcmul($cartGoods->price, $cartGoods->number, 2);

            $couponInfo = [
                'id' => 0,
                'shop_id' => 0,
                'denomination' => 0,
            ];
            if ($coupon && $coupon->goods_id == $cartGoods->goods_id) {
                $couponInfo = [
                    'id' => $coupon->id,
                    'shop_id' => $coupon->shop_id,
                    'denomination' => $coupon->denomination,
                ];
            }

            // 收入基数：平台券，按商品总价计算
            $incomeBase = ($couponInfo['shop_id'] == 0)
                ? $totalPrice
                : max(0, bcsub($totalPrice, $couponInfo['denomination'], 2));

            // 佣金率
            $salesCommissionRate = bcdiv($cartGoods->sales_commission_rate, 100, 4);
            $salesCommission = bcmul($incomeBase, $salesCommissionRate, 2);

            // 运费
            $freightPrice = 0;
            if ($deliveryMode == 1 && $cartGoods->freight_template_id) {
                $freightTemplate = $freightTemplateList->get($cartGoods->freight_template_id);
                $freightPrice = FreightTemplateService::getInstance()
                    ->calcFreightPrice($freightTemplate, $address, $totalPrice, $cartGoods->number);
            }

            // 销售额：商品总价 - 店铺优惠券金额 + 运费
            $salesAmount = ($couponInfo['shop_id'] == 0)
                ? bcadd($totalPrice, $freightPrice, 2)
                : max(0, bcadd(bcsub($totalPrice, $couponInfo['denomination'], 2), $freightPrice, 2));

            // 商家最终收入: 基数 - 平台佣金 + 运费
            $incomeAmount = max(0, bcadd(bcsub($incomeBase, $salesCommission, 2), $freightPrice, 2));

            // 保存记录
            $income = ShopIncome::new();
            $income->shop_id = $shopId;
            $income->order_id = $orderId;
            $income->order_sn = $orderSn;
            $income->goods_id = $cartGoods->goods_id;
            $income->refund_status = $cartGoods->refund_status;
            $income->total_price = $totalPrice;
            $income->coupon_id = $couponInfo['id'];
            $income->coupon_shop_id = $couponInfo['shop_id'];
            $income->coupon_denomination = $couponInfo['denomination'];
            $income->freight_price = $freightPrice;
            $income->sales_amount = $salesAmount;
            $income->income_base = $incomeBase;
            $income->sales_commission_rate = $cartGoods->sales_commission_rate;
            $income->income_amount = $incomeAmount;
            $income->save();
        }
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

    public function dailyIncomeList($shopId, $statusList)
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(17);

        return $this->getShopIncomeQuery($shopId, $statusList)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as created_at'),
                DB::raw('SUM(income_amount) as sum')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();
    }

    public function monthlyIncomeList($shopId, $statusList)
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subMonths(12)->startOfMonth();

        return $this->getShopIncomeQuery($shopId, $statusList)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw("SUM(income_amount) as sum")
            )
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy('month', 'asc')
            ->get();
    }

    public function dailyIncomeGrowthRate($shopId, $statusList)
    {
        $query = $this->getShopIncomeQuery($shopId, $statusList);

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $todayIncomeAmount = (clone $query)->whereDate('created_at', $today)->sum('income_amount');
        $yesterdayIncomeAmount = (clone $query)->whereDate('created_at', $yesterday)->sum('income_amount');

        if ($yesterdayIncomeAmount > 0) {
            $dailyGrowthRate = round((($todayIncomeAmount - $yesterdayIncomeAmount) / $yesterdayIncomeAmount) * 100);
        } else {
            $dailyGrowthRate = 0;
        }

        return $dailyGrowthRate;
    }

    public function weeklyIncomeGrowthRate($shopId, $statusList)
    {
        $query = $this->getShopIncomeQuery($shopId, $statusList);

        $startOfThisWeek = Carbon::now()->startOfWeek();
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        $thisWeekIncomeAmount = (clone $query)->whereBetween('created_at', [$startOfThisWeek, now()])->sum('income_amount');
        $lastWeekIncomeAmount = (clone $query)->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->sum('income_amount');

        if ($lastWeekIncomeAmount > 0) {
            $weeklyGrowthRate = round((($thisWeekIncomeAmount - $lastWeekIncomeAmount) / $lastWeekIncomeAmount) * 100);
        } else {
            $weeklyGrowthRate = 0; // 防止除以零
        }

        return $weeklyGrowthRate;
    }

    public function applyWithdrawal($shopId, $withdrawalId)
    {
        $incomeList = $this->getWithdrawingList($shopId);
        /** @var ShopIncome $income */
        foreach ($incomeList as $income) {
            $income->withdrawal_id = $withdrawalId;
            $income->status = 3;
            $income->save();
        }
    }

    public function finishWithdrawal($shopId, $withdrawalId)
    {
        $incomeList = $this->getWithdrawingList($shopId);
        /** @var ShopIncome $income */
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
        return ShopIncome::query()->where('shop_id', $shopId)->whereIn('status', $statusList);
    }

    public function updateListToPaidStatus(array $orderIds)
    {
        return ShopIncome::query()
            ->whereIn('order_id', $orderIds)
            ->where('status', 0)
            ->update(['status' => 1]);
    }

    public function updateListToConfirmStatus($orderIds, $role = 'user')
    {
        $incomeList = $this->getPaidListByOrderIds($orderIds);
        return $incomeList->map(function (ShopIncome $income) use ($role) {
            if ($income->refund_status == 1 && $role == 'user') {
                // 7天无理由商品：确认收货7天后更新收益状态
                dispatch(new ShopIncomeConfirmJob($income->id));
            } else {
                $income->status = 2;
                $income->save();
            }
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
        return ShopIncome::query()->whereIn('order_id', $orderIds)->where('status', 1)->get($columns);
    }

    public function getPaidIncomeById($id, $columns = ['*'])
    {
        return ShopIncome::query()->where('status', 1)->where('id', $id)->first($columns);
    }

    public function deleteListByOrderIds(array $orderIds, $status)
    {
        return ShopIncome::query()
            ->whereIn('order_id', $orderIds)
            ->where('status', $status)
            ->delete();
    }

    public function deleteIncome($orderId, $goodsId, $status)
    {
        return ShopIncome::query()
            ->where('order_id', $orderId)
            ->where('goods_id', $goodsId)
            ->where('status', $status)
            ->delete();
    }
}
