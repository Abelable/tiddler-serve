<?php

namespace App\Services;

use App\Models\LiveGoods;

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
}
