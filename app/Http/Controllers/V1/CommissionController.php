<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\CommissionService;
use App\Services\PromoterService;
use App\Services\RelationService;
use App\Utils\CodeResponse;
use Illuminate\Support\Carbon;

class CommissionController extends Controller
{
    public function achievement()
    {
        $promoterInfo = $this->user()->promoterInfo;

        if (is_null($promoterInfo)) {
            return $this->fail(CodeResponse::FAIL, '非推广员无法查看数据');
        }

        if ($promoterInfo->level_change_time) {
            $totalGMV = CommissionService::getInstance()->getUserGMVByTimeType($this->userId(), 7, $promoterInfo->level_change_time);
        } else {
            $totalGMV = CommissionService::getInstance()->getUserGMVByTimeType($this->userId(), 6);
        }

        // 推广员升C1：3个月累计超3w
        // C1升C2：3个月累计超20w
        // C2生C3：3个月累计超60w
        $level = $promoterInfo->level;
        $targets = [1 => 30000, 2 => 200000, 3 => 600000];
        $target = $targets[$level] ?? 0;
        $percent = ($totalGMV >= $target) ? 100 : round(($totalGMV / $target) * 100, 2);

        return $this->success([
            'totalGMV' => $totalGMV,
            'percent' => $percent
        ]);
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
        $salesVolume = (clone $query)->whereIn('status', [1, 2, 3, 4])->sum('commission_base');
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
        $salesVolume = (clone $query)->whereIn('status', [1, 2, 3, 4])->sum('commission_base');

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
        $share = (clone $commissionQuery)->where('scene', 2)->sum('commission_amount');
        return $this->success([
            'selfPurchase' => $selfPurchase,
            'share' => $share
        ]);
    }
}
