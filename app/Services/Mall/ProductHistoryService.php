<?php

namespace App\Services\Mall;

use App\Models\Mall\ProductHistory;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProductHistoryService extends BaseService
{

    public function getHistoryPage($userId, $type, PageInput $input, $columns = ['*'])
    {
        return ProductHistory::query()
            ->where('user_id', $userId)
            ->where('product_type', $type)
            ->orderBy('updated_at', $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function createHistory($userId, $productType, $productId)
    {
        $history = $this->getHistory($userId, $productType, $productId);

        if (!is_null($history)) {
            $history->count = $history->count + 1;
        } else {
            $history = ProductHistory::new();
            $history->user_id = $userId;
            $history->product_type = $productType;
            $history->product_id = $productId;
        }
        $history->save();

        return $history;
    }

    public function getHistory($userId, $productType, $productId, $columns = ['*'])
    {
        return ProductHistory::query()
            ->where('user_id', $userId)
            ->where('product_type', $productType)
            ->where('product_id', $productId)
            ->first($columns);
    }

    public function getHistoryDateCount($productType, $productIds, $dateDesc = 'today')
    {
        switch ($dateDesc) {
            case 'today':
                $date = Carbon::today();
                break;
            case 'yesterday':
                $date = Carbon::yesterday();
                break;
        }

        return ProductHistory::query()
            ->where('product_type', $productType)
            ->whereIn('product_id', $productIds)
            ->whereDate('updated_at', $date)
            ->count();
    }

    public function countSum($productType, $productIds)
    {
        return ProductHistory::query()
            ->where('product_type', $productType)
            ->whereIn('product_id', $productIds)
            ->sum('count');
    }

    public function dailyCountList($productType, $productIds)
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(17);

        return ProductHistory::query()
            ->where('product_type', $productType)
            ->whereIn('product_id', $productIds)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(updated_at) as updated_at'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('DATE(updated_at)'))
            ->get();
    }

    public function dailyCountGrowthRate($productType, $productIds)
    {
        $query = ProductHistory::query()
            ->where('product_type', $productType)
            ->whereIn('product_id', $productIds);

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $todayCount = (clone $query)->whereDate('updated_at', $today)->count();
        $yesterdayCount = (clone $query)->whereDate('updated_at', $yesterday)->count();

        if ($yesterdayCount > 0) {
            $dailyGrowthRate = round((($todayCount - $yesterdayCount) / $yesterdayCount) * 100);
        } else {
            $dailyGrowthRate = 0;
        }

        return $dailyGrowthRate;
    }

    public function weeklyCountGrowthRate($productType, $productIds)
    {
        $query = ProductHistory::query()
            ->where('product_type', $productType)
            ->whereIn('product_id', $productIds);

        $startOfThisWeek = Carbon::now()->startOfWeek();
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        $thisWeekCount = (clone $query)->whereBetween('updated_at', [$startOfThisWeek, now()])->count();
        $lastWeekCount = (clone $query)->whereBetween('updated_at', [$startOfLastWeek, $endOfLastWeek])->count();

        if ($lastWeekCount > 0) {
            $weeklyGrowthRate = round((($thisWeekCount - $lastWeekCount) / $lastWeekCount) * 100);
        } else {
            $weeklyGrowthRate = 0; // 防止除以零
        }

        return $weeklyGrowthRate;
    }
}
