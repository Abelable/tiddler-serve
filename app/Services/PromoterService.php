<?php

namespace App\Services;

use App\Models\OrderGoods;
use App\Models\Promoter;
use App\Utils\Inputs\Admin\UserPageInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PromoterService extends BaseService
{
    public function createPromoterByGift($orderGoodsId)
    {
        /** @var OrderGoods $orderGoods */
        $orderGoods = OrderGoodsService::getInstance()->getById($orderGoodsId);
        $promoter = $this->createPromoter(
            $orderGoods->user_id,
            2,
            $orderGoods->duration,
            $orderGoods->order_id,
            $orderGoods->goods_id
        );
        PromoterChangeLogService::getInstance()->createLog($promoter->id, 1);

        $superiorId = RelationService::getInstance()->getSuperiorId($orderGoods->goods_id);
        return $promoter;
    }

    public function renewPromoterByGift($orderGoodsId)
    {
        /** @var OrderGoods $orderGoods */
        $orderGoods = OrderGoodsService::getInstance()->getById($orderGoodsId);
        $promoter = $this->getPromoterByUserId($orderGoods->user_id);

        $expirationTime = Carbon::parse($promoter->expiration_time)
            ->addDays($orderGoods->duration)
            ->setTimezone('UTC')
            ->format('Y-m-d\TH:i:s.v\Z');

        PromoterChangeLogService::getInstance()
            ->createLog(
                $promoter->id,
                2,
                0,
                1,
                $promoter->expiration_time,
                $expirationTime,
                $promoter->gift_goods_id,
                $orderGoodsId
            );

        return $this->renewPromoter($promoter, $orderGoods, $expirationTime);
    }

    public function createPromoter($userId, $path, $duration, $orderId = null, $giftGoodsId = null)
    {
        $expirationTime = Carbon::now()
            ->addDays($duration)
            ->setTimezone('UTC')
            ->format('Y-m-d\TH:i:s.v\Z');

        $promoter = Promoter::new();
        $promoter->user_id = $userId;
        $promoter->path = $path;
        $promoter->expiration_time = $expirationTime;
        $promoter->order_id = $orderId ?: 0;
        $promoter->gift_goods_id = $giftGoodsId ?: 0;
        $promoter->save();

        return $promoter;
    }

    public function renewPromoter(Promoter $promoter, OrderGoods $orderGoods, $expirationTime)
    {
        $promoter->expiration_time = $expirationTime;
        $promoter->order_id = $orderGoods->order_id;
        $promoter->gift_goods_id = $orderGoods->goods_id;
        $promoter->save();
        return $promoter;
    }

    public function adminCreate($userId, $level, $scene, $duration)
    {
        $expirationTime = Carbon::now()
            ->addDays($duration)
            ->setTimezone('UTC')
            ->format('Y-m-d\TH:i:s.v\Z');

        $promoter = Promoter::new();
        $promoter->user_id = $userId;
        $promoter->level = $level;
        $promoter->scene = $scene;
        $promoter->path = 1;
        $promoter->expiration_time = $expirationTime;
        $promoter->save();

        return $promoter;
    }

    public function getPromoterLevel($userId)
    {
        $promoter = $this->getPromoterByUserId($userId);
        return $promoter ? $promoter->level : 0;
    }

    public function getPromoterByUserId($userId, $columns = ['*'])
    {
        return Promoter::query()->where('user_id', $userId)->whereIn('status', [1, 2])->first($columns);
    }

    public function getPromoterById($id, $columns = ['*'])
    {
        return Promoter::query()->find($id, $columns);
    }

    public function getExactPromoter($userId, $level, $scene, $columns = ['*'])
    {
        return Promoter::query()
            ->where('user_id', $userId)
            ->where('level', $level)
            ->where('scene', $scene)
            ->first($columns);
    }

    public function getPromoterPage(UserPageInput $input, $columns = ['*'])
    {
        $query = Promoter::query();
        if (!empty($input->level)) {
            $query->where('level', $input->level);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getOptions($columns = ['*'])
    {
        return Promoter::query()->get($columns);
    }

    public function getPromoterCountByUserIds(array $userIds, array $statusList = [1, 2])
    {
        return Promoter::query()
            ->whereIn('status', $statusList)
            ->whereIn('user_id', $userIds)
            ->count();
    }

    public function getPromoterListByUserIds(
        array $userIds,
        array $statusList = [1, 2],
        $columns = ['*']
    )
    {
        return Promoter::query()
            ->whereIn('status', $statusList)
            ->whereIn('user_id', $userIds)
            ->get($columns);
    }

    public function getPromoterPageByUserIds(
        array $userIds,
        PageInput $input,
        array $statusList = [1, 2],
        $columns = ['*']
    )
    {
        return Promoter::query()
            ->whereIn('status', $statusList)
            ->whereIn('user_id', $userIds)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function promoterCountSum(array $statusList = [1, 2])
    {
        return Promoter::query()->whereIn('status', $statusList)->count();
    }

    public function dailyPromoterCountList(array $statusList = [1, 2])
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(17);

        return Promoter::query()
            ->whereIn('status', $statusList)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as created_at'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();
    }

    public function dailyPromoterCountGrowthRate(array $statusList = [1, 2])
    {
        $query = Promoter::query()->whereIn('status', $statusList);

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $todayPromoterCount = (clone $query)->whereDate('created_at', $today)->count();
        $yesterdayPromoterCount = (clone $query)->whereDate('created_at', $yesterday)->count();

        if ($yesterdayPromoterCount > 0) {
            $dailyGrowthRate = round((($todayPromoterCount - $yesterdayPromoterCount) / $yesterdayPromoterCount) * 100);
        } else {
            $dailyGrowthRate = 0;
        }

        return $dailyGrowthRate;
    }

    public function weeklyPromoterCountGrowthRate(array $statusList = [1, 2])
    {
        $query = Promoter::query()->whereIn('status', $statusList);

        $startOfThisWeek = Carbon::now()->startOfWeek();
        $startOfLastWeek = Carbon::now()->subWeek()->startOfWeek();
        $endOfLastWeek = Carbon::now()->subWeek()->endOfWeek();

        $thisWeekPromoterCount = (clone $query)->whereBetween('created_at', [$startOfThisWeek, now()])->count();
        $lastWeekPromoterCount = (clone $query)->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();

        if ($lastWeekPromoterCount > 0) {
            $weeklyGrowthRate = round((($thisWeekPromoterCount - $lastWeekPromoterCount) / $lastWeekPromoterCount) * 100);
        } else {
            $weeklyGrowthRate = 0; // 防止除以零
        }

        return $weeklyGrowthRate;
    }

    public function getRecentlyPromoter($userId, $days = 7, array $statusList = [1, 2], $columns = ['*'])
    {
        return Promoter::query()
            ->where('user_id', $userId)
            ->whereIn('status', $statusList)
            ->where('created_at', '>=', now()->subDays($days))
            ->first($columns);
    }

    public function getUserPromoterByPathList($userId, array $pathList, array $statusList = [1, 2], $columns = ['*'])
    {
        return Promoter::query()
            ->where('user_id', $userId)
            ->whereIn('status', $statusList)
            ->whereIn('path', $pathList)
            ->first($columns);
    }

    public function getPromoterLevelsCount(array $statusList = [1, 2])
    {
        return Promoter::query()
            ->whereIn('status', $statusList)
            ->select('level', DB::raw('COUNT(*) as number'))
            ->whereIn('level', [1, 2, 3, 4])
            ->groupBy('level')
            ->get();
    }

    public function getTopPromoterPage(PageInput $input, array $statusList = [1, 2], $columns = ['*'])
    {
        return Promoter::query()
            ->whereIn('status', $statusList)
            ->select('*', DB::raw('(self_commission_sum + share_commission_sum + team_commission_sum) as total_commission'))
            ->orderByDesc('sub_user_number')
            ->orderByDesc('total_commission')
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function updateSupUserCount($userId, $count = 1)
    {
        $promoter = $this->getPromoterByUserId($userId);
        $promoter->sub_user_number = $promoter->sub_user_number + $count;
        $promoter->save();
        return $promoter;
    }

    public function updateSupPromoterCount($userId, $count = 1)
    {
        $promoter = $this->getPromoterByUserId($userId);
        $promoter->sub_promoter_number = $promoter->sub_promoter_number + $count;
        $promoter->save();
        return $promoter;
    }

    public function updateAchievement($userId, $achievement)
    {
        $promoter = $this->getPromoterByUserId($userId);
        $promoter->achievement = bcadd($promoter->achievement, $achievement, 2);
        $promoter->save();
        return $promoter;
    }

    public function updateSelfCommissionSum($userId, $commission)
    {
        $promoter = $this->getPromoterByUserId($userId);
        $promoter->self_commission_sum = bcadd($promoter->self_commission_sum, $commission, 2);
        $promoter->save();
        return $promoter;
    }

    public function updateShareCommissionSum($userId, $commission)
    {
        $promoter = $this->getPromoterByUserId($userId);
        $promoter->share_commission_sum = bcadd($promoter->share_commission_sum, $commission, 2);
        $promoter->save();
        return $promoter;
    }

    public function updateTeamCommissionSum($userId, $commission)
    {
        $promoter = $this->getPromoterByUserId($userId);
        $promoter->team_commission_sum = bcadd($promoter->team_commission_sum, $commission, 2);
        $promoter->save();
        return $promoter;
    }
}
