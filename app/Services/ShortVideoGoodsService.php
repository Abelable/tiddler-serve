<?php

namespace App\Services;

use App\Models\ShortVideoGoods;

class ShortVideoGoodsService extends BaseService
{
    public function newGoods($videoId, $goodsId)
    {
        $goods = ShortVideoGoods::new();
        $goods->video_id = $videoId;
        $goods->goods_id = $goodsId;
        $goods->save();
        return $goods;
    }

    public function list($videoId, $columns = ['*'])
    {
        return ShortVideoGoods::query()->where('video_id', $videoId)->get($columns);
    }

    public function goodsIds($videoId)
    {
        $list = $this->list($videoId);
        return $list->pluck('goods_id')->toArray();
    }
}
