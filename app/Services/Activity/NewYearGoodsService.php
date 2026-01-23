<?php

namespace App\Services\Activity;

use App\Models\Activity\NewYearGoods;
use App\Models\Mall\Goods\Goods;
use App\Services\BaseService;
use App\Utils\Inputs\Activity\NewYearGoodsBaseInput;
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

    public function getFilterGoodsList(NewYearGoodsBaseInput $input, $columns = ['*'])
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

    public function create(NewYearGoodsBaseInput $input, Goods $goods)
    {
        $newYearGoods = NewYearGoods::new();
        $newYearGoods->goods_id = $goods->id;
        $newYearGoods->cover = $goods->cover;
        $newYearGoods->name = $goods->name;
        $newYearGoods->luck_score = $input->luckScore;
        if (!empty($input->stock)) {
            $newYearGoods->stock = $input->stock;
        }
        if (!empty($input->limit)) {
            $newYearGoods->limit = $input->stock;
        }
        $newYearGoods->save();

        return $newYearGoods;
    }

    public function update(NewYearGoods $newYearGoods, NewYearGoodsInput $input)
    {
        if (!empty($input->cover)) {
            $newYearGoods->cover = $input->cover;
        }
        if (!empty($input->name)) {
            $newYearGoods->name = $input->name;
        }
        if (!empty($input->luckScore)) {
            $newYearGoods->luck_score = $input->luckScore;
        }
        if (!empty($input->stock)) {
            $newYearGoods->stock = $input->stock;
        }
        if (!empty($input->limit)) {
            $newYearGoods->limit = $input->limit;
        }
        $newYearGoods->save();

        return $newYearGoods;
    }

    public function updateLuckScore($id, $luckScore)
    {
        NewYearGoods::query()->where('id', $id)->update(['luck_score' => $luckScore]);
    }

    public function updateLuckStock($id, $stock)
    {
        NewYearGoods::query()->where('id', $id)->update(['luck_score' => $stock]);
    }

    public function updateSort($id, $sort)
    {
        NewYearGoods::query()->where('id', $id)->update(['sort' => $sort]);
    }

    public function decreaseStock(int $id)
    {
        NewYearGoods::query()
            ->where('id', $id)
            ->where('stock', '>', 0)
            ->decrement('stock', 1);
    }
}
