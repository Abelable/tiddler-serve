<?php

namespace App\Services\Activity;

use App\Models\Activity\NewYearPrize;
use App\Models\Activity\NewYearUserPrize;
use App\Services\BaseService;
use App\Utils\Inputs\Activity\NewYearPrizeInput;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\TypePageInput;

class NewYearPrizeService extends BaseService
{
    public function getPage(TypePageInput $input, $columns = ['*'])
    {
        $query = NewYearPrize::query();
        if (!empty($input->type)) {
            $query = $query->where('type', $input->type);
        }
        return $query
            ->orderBy('sort', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getList($columns = ['*'])
    {
        return NewYearPrize::query()
            ->where('status', 1)
            ->orderBy('sort', 'desc')
            ->orderBy('id', 'asc')
            ->get($columns);
    }

    public function getDrawList(int $userId)
    {
        $now = now();

        $prizeList = NewYearPrize::query()
            ->where('status', 1)
            ->orderBy('rate', 'asc')
            ->get();

        $prizeIds = $prizeList->pluck('id')->toArray();
        $countMap = $this->getUserPrizeCountMap($userId, $prizeIds);

        $cumulative = 0;
        $prizeList = $prizeList->filter(function (NewYearPrize $prize) use ($userId, $now, $countMap) {
            // 时间窗口
            if ($prize->start_at && $prize->start_at->gt($now)) {
                return false;
            }
            if ($prize->end_at && $prize->end_at->lt($now)) {
                return false;
            }

            // 库存
            if ($prize->stock === 0) {
                return false;
            }

            // 单人上限
            if ($prize->limit_per_user > 0) {
                $count = $countMap[$prize->id] ?? 0;
                if ($count >= $prize->limit_per_user) {
                    return false;
                }
            }

            return true;
        })->values();

        foreach ($prizeList as $prize) {
            $cumulative += $prize->rate;
            $prize->cumulative_rate = $cumulative;
        }

        return $prizeList;
    }

    public function updatePrize(NewYearPrize $prize, NewYearPrizeInput $input)
    {
        $prize->status = $input->status ?? $prize->status;
        $prize->type = $input->type;
        $prize->coupon_id = $input->couponId ?? 0;
        $prize->goods_id = $input->goodsId ?? 0;
        $prize->is_big = $input->isBig ?? 0;
        $prize->cover = $input->cover;
        $prize->name = $input->name;
        $prize->sort = $input->sort ?? $prize->sort;

        // 抽奖核心字段
        $prize->rate = $input->rate ?? $prize->rate;
        $prize->stock = $input->stock ?? $prize->stock;
        $prize->luck_score = $input->luckScore ?? $prize->luck_score;
        $prize->cost = $input->cost ?? $prize->cost;

        // 风控字段
        $prize->limit_per_user = $input->limitPerUser ?? $prize->limit_per_user;
        $prize->fallback_prize_id = $input->fallbackPrizeId ?? $prize->fallback_prize_id;

        // 时间控制
        $prize->start_at = $input->startAt ?? $prize->start_at;
        $prize->end_at = $input->endAt ?? $prize->end_at;

        $prize->save();

        return $prize;
    }

    public function getPrizeById($id, $columns = ['*'])
    {
        return NewYearPrize::query()->find($id, $columns);
    }

    public function updateIsBig($id, $isBig)
    {
        NewYearPrize::query()->where('id', $id)->update(['is_big' => $isBig]);
    }

    public function updateSort($id, $sort)
    {
        NewYearPrize::query()->where('id', $id)->update(['sort' => $sort]);
    }

    public function decreaseStock(int $prizeId): bool
    {
        $prize = NewYearPrize::query()
            ->select(['id', 'stock'])
            ->where('id', $prizeId)
            ->first();

        if (!$prize) {
            return false;
        }

        // 无限库存，直接成功
        if ((int) $prize->stock === -1) {
            return true;
        }

        // 原子扣减，防止并发超卖
        $affected = NewYearPrize::query()
            ->where('id', $prizeId)
            ->where('stock', '>', 0)
            ->decrement('stock', 1);

        return $affected === 1;
    }

    public function createUserPrize($userId, NewYearPrize $prize)
    {
        $userPrize = NewYearUserPrize::new();
        $userPrize->user_id = $userId;
        $userPrize->prize_id = $prize->id;
        $userPrize->prize_type = $prize->type;
        $userPrize->cover = $prize->cover;
        $userPrize->name = $prize->name;
        $userPrize->coupon_id = $prize->coupon_id;
        $userPrize->goods_id = $prize->goods_id;
        $userPrize->save();

        return $userPrize;
    }

    public function getUserPrizePage($userId, PageInput $input, $columns = ['*'])
    {
        return NewYearUserPrize::query()
            ->where('user_id', $userId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserPrize($userId, $id, $columns = ['*'])
    {
        return NewYearUserPrize::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function useCouponPrize($userId, $couponId, $columns = ['*'])
    {
        $prize = NewYearUserPrize::query()
            ->where('user_id', $userId)
            ->where('prize_type', 2)
            ->where('coupon_id', $couponId)
            ->where('status', 0)
            ->first($columns);
        if (!is_null($prize)) {
            $prize->status = 1;
            $prize->save();
        }
        return $prize;
    }

    public function restoreCouponPrize($userId, $couponId, $columns = ['*'])
    {
        $prize = NewYearUserPrize::query()
            ->where('user_id', $userId)
            ->where('prize_type', 2)
            ->where('coupon_id', $couponId)
            ->where('status', 1)
            ->first($columns);
        if (!is_null($prize)) {
            $prize->status = 0;
            $prize->save();
        }
        return $prize;
    }

    public function getUserPrizeCount($userId, $prizeIds, $statusList = [0, 1])
    {
        return NewYearUserPrize::query()
            ->where('user_id', $userId)
            ->whereIn('prize_id', $prizeIds)
            ->whereIn('status', $statusList)
            ->count();
    }

    public function getUserPrizeCountMap($userId, array $prizeIds, array $statusList = [0, 1])
    {
        return NewYearUserPrize::query()
            ->selectRaw('prize_id, COUNT(*) as cnt')
            ->where('user_id', $userId)
            ->whereIn('prize_id', $prizeIds)
            ->whereIn('status', $statusList)
            ->groupBy('prize_id')
            ->pluck('cnt', 'prize_id')
            ->toArray();
    }
}
