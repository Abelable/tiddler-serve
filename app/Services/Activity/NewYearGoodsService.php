<?php

namespace App\Services\Activity;

use App\Models\Activity\NewYearGoods;
use App\Models\Mall\Goods\Goods;
use App\Services\BaseService;
use App\Utils\Inputs\Activity\NewYearGoodsInput;
use App\Utils\Inputs\PageInput;

class NewYearGoodsService extends BaseService
{
    public function getPage(PageInput $input, $columns = ['*'])
    {
        return NewYearGoods::query()
            ->orderBy('sort', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getList($columns = ['*'])
    {
        return NewYearGoods::query()
            ->where('status', 1)
            ->orderBy('sort', 'desc')
            ->orderBy('id', 'asc')
            ->get($columns);
    }

    public function getFilterGoodsList(NewYearGoodsInput $input, $columns = ['*'])
    {
        return NewYearGoods::query()
            ->whereIn('goods_id', $input->goodsIds)
            ->get($columns);
    }

    public function getGoodsByGoodsId($goodsId, $columns = ['*'])
    {
        return NewYearGoods::query()->where('goods_id', $goodsId)->first($columns);
    }

    public function getGoodsById($id, $columns = ['*'])
    {
        return NewYearGoods::query()->find($id, $columns);
    }

    public function create(NewYearGoodsInput $input, Goods $goods)
    {
        $giftGoods = NewYearGoods::new();
        $giftGoods->goods_id = $goods->id;
        $giftGoods->cover = $goods->cover;
        $giftGoods->name = $goods->name;
        $giftGoods->luck_score = $input->luckScore;
        $giftGoods->save();

        return $giftGoods;
    }

    public function updateLuckScore($id, $luckScore)
    {
        NewYearGoods::query()->where('id', $id)->update(['luck_score' => $luckScore]);
    }

    public function updateSort($id, $sort)
    {
        NewYearGoods::query()->where('id', $id)->update(['sort' => $sort]);
    }
}
