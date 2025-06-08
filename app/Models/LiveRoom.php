<?php

namespace App\Models;

use Laravel\Scout\Searchable;

class LiveRoom extends BaseModel
{
    use Searchable;

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'anchorName' => $this->anchorInfo->nickname,
        ];
    }

    public function anchorInfo()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'avatar', 'nickname');
    }

    public function goodsList()
    {
        return $this
            ->belongsToMany(Goods::class, 'live_goods', 'room_id', 'goods_id')
            ->whereNull('live_goods.deleted_at')
            ->select('goods.id', 'goods.cover', 'goods.name', 'goods.price', 'goods.market_price', 'goods.stock');
    }

    public function hotGoods()
    {
        return $this
            ->belongsToMany(Goods::class, 'live_goods', 'room_id', 'goods_id')
            ->whereNull('live_goods.deleted_at')
            ->wherePivot('live_goods.is_hot', 1)
            ->select('goods.id', 'goods.cover', 'goods.name', 'goods.price', 'goods.market_price', 'goods.stock')
            ->first();
    }
}
