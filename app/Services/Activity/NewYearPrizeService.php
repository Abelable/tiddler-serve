<?php

namespace App\Services\Activity;

use App\Models\Activity\NewYearPrize;
use App\Services\BaseService;
use App\Utils\Inputs\Activity\NewYearPrizeInput;
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
}
