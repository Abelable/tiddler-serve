<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Services\CommissionService;
use App\Services\OrderGoodsService;
use App\Services\OrderService;
use App\Services\PromoterService;
use App\Services\RelationService;
use App\Utils\CodeResponse;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Carbon;

class CommissionController extends Controller
{
    public function achievement()
    {
        $promoterInfo = $this->user()->promoterInfo;

        if (is_null($promoterInfo)) {
            return $this->fail(CodeResponse::FAIL, '非推广员无法查看数据');
        }

        $monthDifference = 2;
        if ($promoterInfo->level_change_time) {
            $currentMonth = date('n');
            $levelChangeMonth = (int)date('n', strtotime($promoterInfo->level_change_time));
            $monthDifference = $currentMonth - $levelChangeMonth;
            if ($monthDifference < 0) {
                $monthDifference += 12;
            }
        }

        if ($monthDifference == 0) {
            $beforeLastMonthGMV = 0;
            $lastMonthGMV = 0;
        } elseif ($monthDifference == 1) {
            $beforeLastMonthGMV = 0;
            $lastMonthGMV = CommissionService::getInstance()->getUserGMVByTimeType($this->userId(), 4);
        } else {
            $beforeLastMonthGMV = CommissionService::getInstance()->getUserGMVByTimeType($this->userId(), 5);
            $lastMonthGMV = CommissionService::getInstance()->getUserGMVByTimeType($this->userId(), 4);
        }
        $curMonthGMV = CommissionService::getInstance()->getUserGMVByTimeType($this->userId(), 3);

        $totalGMV = bcadd($beforeLastMonthGMV, $lastMonthGMV, 2);
        $totalGMV = bcadd($totalGMV, $curMonthGMV, 2);

        // 推广员升C1：3个月累计超3w
        // C1升C2：3个月累计超20w
        // C2生C3：3个月累计超60w
        $level = $promoterInfo->level;
        $targets = [1 => 30000, 2 => 200000, 3 => 600000];
        $target = $targets[$level] ?? 0;
        $percent = ($totalGMV >= $target) ? 100 : round(($totalGMV / $target) * 100, 2);

        return $this->success([
            'monthDifference' => $monthDifference,
            'beforeLastMonthGMV' => $beforeLastMonthGMV,
            'lastMonthGMV' => $lastMonthGMV,
            'curMonthGMV' => $curMonthGMV,
            'totalGMV' => $totalGMV,
            'percent' => $percent
        ]);
    }

    public function commissionOrderList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $scene = $this->verifyInteger('scene');
        $timeType = $this->verifyRequiredInteger('timeType');
        $statusList = $this->verifyArray('statusList');

        $page = CommissionService::getInstance()
            ->getUserCommissionListByTimeType($this->userId(), $timeType, $statusList,$input, $scene ?: null);
        $commissionList = collect($page->items());

        $productIdsByType = $commissionList->groupBy('product_type')->map(function ($group) {
            return $group->pluck('product_id')->unique()->toArray();
        });

        $goodsMap = [];
        if (!empty($productIdsByType[ProductType::GOODS])) {
            $goodsColumns = ['order_id', 'goods_id', 'cover', 'name', 'selected_sku_name', 'price', 'number'];
            $goodsList = OrderGoodsService::getInstance()
                ->getListByGoodsIds($productIdsByType[ProductType::GOODS], $goodsColumns);
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
        }

        $list = $commissionList->map(function (Commission $commission) use ($goodsMap) {
            $product = null;
            $orderId = $commission->order_id;
            $productId = $commission->product_id;

            switch ($commission->product_type) {
                case ProductType::SCENIC:
                    $product = $scenicMap[$orderId][$productId] ?? null;
                    break;
                case ProductType::HOTEL:
                    $product = $hotelMap[$orderId][$productId] ?? null;
                    break;
                case ProductType::RESTAURANT:
                    $product = $restaurantMap[$orderId][$productId] ?? null;
                    break;
                case ProductType::GOODS:
                    $product = $goodsMap[$orderId][$productId] ?? null;
                    break;
            }

            $commission['product'] = $product;
            unset($commission['product_id']);

            return $commission;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function sum()
    {
        $cashAmount = CommissionService::getInstance()
            ->getUserCommissionQuery([$this->userId()], [2])
            ->whereMonth('created_at', '!=', Carbon::now()->month)
            ->sum('commission_amount');
        $pendingAmount = CommissionService::getInstance()->getUserCommissionSum($this->userId(), [1]);
        $settledAmount = CommissionService::getInstance()->getUserCommissionSum($this->userId(), [2, 3, 4]);
        return $this->success([
            'cashAmount' => $cashAmount,
            'pendingAmount' => $pendingAmount,
            'settledAmount' => $settledAmount
        ]);
    }

    public function timeData()
    {
        $timeType = $this->verifyRequiredInteger('timeType');
        $scene = $this->verifyInteger('scene');

        $query = CommissionService::getInstance()->getUserCommissionQueryByTimeType([$this->userId()], $timeType);
        if (!is_null($scene)) {
            switch ($scene) {
                case 1:
                    $query = $query->where('scene', 1);
                    break;
                case 2:
                    $query = $query->whereIn('scene', [2, 3]);
                    break;
                case 3:
                    $query = $query->whereIn('scene', [4, 5]);
                    break;
            }
        }

        $orderCount = (clone $query)->whereIn('status', [1, 2, 3, 4])->distinct('order_id')->count('order_id');
        $salesVolume = (clone $query)->whereIn('status', [1, 2, 3, 4])->sum('payment_amount');
        $pendingAmount = (clone $query)->where('status', 1)->sum('commission_amount');
        $settledAmount = (clone $query)->whereIn('status', [2, 3, 4])->sum('commission_amount');

        return $this->success([
            'orderCount' => $orderCount,
            'salesVolume' => $salesVolume,
            'pendingAmount' => $pendingAmount,
            'settledAmount' => $settledAmount
        ]);
    }

    public function teamTimeData()
    {
        $timeType = $this->verifyRequiredInteger('timeType');

        $customerIds = RelationService::getInstance()->getListBySuperiorId($this->userId())->pluck('fan_id')->toArray();
        $promoterIds = PromoterService::getInstance()->getPromoterListByUserIds($customerIds)->pluck('user_id')->toArray();

        $query = CommissionService::getInstance()->getUserCommissionQueryByTimeType($promoterIds, $timeType);
        $orderCount = (clone $query)->whereIn('status', [1, 2, 3, 4])->distinct('order_id')->count('order_id');
        $salesVolume = (clone $query)->whereIn('status', [1, 2, 3, 4])->sum('payment_amount');

        $pendingAmount = 0;
        $settledAmount = 0;
        if (!is_null($this->user()->promoterInfo)) {
            $pendingGMV = (clone $query)->where('status', 1)->sum('commission_base');
            $settledGMV = (clone $query)->whereIn('status', [2, 3, 4])->sum('commission_base');
            switch ($this->user()->promoterInfo->level) {
                case 2:
                    $pendingAmount = bcmul($pendingGMV, 0.01, 2);
                    $settledAmount = bcmul($settledGMV, 0.01, 2);
                    break;
                case 3:
                    $pendingAmount = bcmul($pendingGMV, 0.02, 2);
                    $settledAmount = bcmul($settledGMV, 0.02, 2);
                    break;
                case 4:
                    $pendingAmount = bcmul($pendingGMV, 0.03, 2);
                    $settledAmount = bcmul($settledGMV, 0.03, 2);
                    break;
            }
        }

        return $this->success([
            'orderCount' => $orderCount,
            'salesVolume' => $salesVolume,
            'pendingAmount' => $pendingAmount,
            'settledAmount' => $settledAmount
        ]);
    }

    public function cash()
    {
        $commissionQuery = CommissionService::getInstance()
            ->getUserCommissionQuery([$this->userId()], [2])
            ->whereMonth('created_at', '!=', Carbon::now()->month);
        $selfPurchase = (clone $commissionQuery)->where('scene', 1)->sum('commission_amount');
        $share = (clone $commissionQuery)->whereIn('scene', [2, 3])->sum('commission_amount');
        $team = (clone $commissionQuery)->whereIn('scene', [4, 5])->sum('commission_amount');
        return $this->success([
            'selfPurchase' => $selfPurchase,
            'share' => $share,
            'team' => $team
        ]);
    }
}
