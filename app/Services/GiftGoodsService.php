<?php

namespace App\Services;

use App\Models\GiftGoods;
use App\Utils\Inputs\GiftGoodsInput;
use App\Utils\Inputs\GiftGoodsPageInput;

class GiftGoodsService extends BaseService
{
    public function getPage(GiftGoodsPageInput $input, $columns = ['*'])
    {
        $query = GiftGoods::query();
        if (!empty($input->typeId)) {
            $query = $query->where('type_id', $input->typeId);
        }
        if (!empty($input->goodsId)) {
            $query = $query->where('goods_id', $input->goodsId);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getList($columns = ['*'])
    {
        return GiftGoods::query()->get($columns);
    }

    public function getFilterGoodsList(GiftGoodsInput $input, $columns = ['*'])
    {
        return GiftGoods::query()
            ->where('type_id', $input->typeId)
            ->whereIn('goods_id', $input->goodsIds)
            ->get($columns);
    }

    public function getGoodsByGoodsId($goodsId, $columns = ['*'])
    {
        return GiftGoods::query()->where('goods_id', $goodsId)->first($columns);
    }

    public function getGoodsById($id, $columns = ['*'])
    {
        return GiftGoods::query()->find($id, $columns);
    }
}
