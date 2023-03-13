<?php

namespace App\Services\Media\Live;

use App\Models\LiveGoods;
use App\Services\BaseService;

class LiveGoodsService extends BaseService
{
    public function newGoods($roomId, $goodsId)
    {
        $goods = LiveGoods::new();
        $goods->room_id = $roomId;
        $goods->goods_id = $goodsId;
        $goods->save();
        return $goods;
    }

    public function list($roomId, $columns = ['*'])
    {
        return LiveGoods::query()->where('room_id', $roomId)->get($columns);
    }

    public function goodsIds($roomId)
    {
        $list = $this->list($roomId);
        return $list->pluck('goods_id')->toArray();
    }

    public function deleteGoods($goodsIds)
    {
        return LiveGoods::query()->whereIn('goods_id', $goodsIds)->delete();
    }
}
