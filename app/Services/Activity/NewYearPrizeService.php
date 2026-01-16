<?php

namespace App\Services\Activity;

use App\Models\Activity\NewYearPrize;
use App\Services\BaseService;
use App\Utils\Inputs\Activity\NewYearPrizeInput;
use App\Utils\Inputs\PageInput;

class NewYearPrizeService extends BaseService
{
    public function getPage(PageInput $input, $columns = ['*'])
    {
        return NewYearPrize::query()
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
        $prize->type = $input->type;
        $prize->coupon_id = $input->couponId ?? 0;
        $prize->goods_id = $input->goodsId ?? 0;
        $prize->is_big = $input->isBig ?? 0;
        $prize->cover = $input->cover;
        $prize->name = $input->name;
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
