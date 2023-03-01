<?php

namespace App\Services\Media\Note;

use App\Models\TourismNoteGoods;
use App\Services\BaseService;

class TourismNoteGoodsService extends BaseService
{
    public function newGoods($noteId, $goodsId)
    {
        $goods = TourismNoteGoods::new();
        $goods->note_id = $noteId;
        $goods->goods_id = $goodsId;
        $goods->save();
        return $goods;
    }

    public function list($noteId, $columns = ['*'])
    {
        return TourismNoteGoods::query()->where('note_id', $noteId)->get($columns);
    }

    public function goodsIds($noteId)
    {
        $list = $this->list($noteId);
        return $list->pluck('goods_id')->toArray();
    }

    public function deleteList($noteId)
    {
        TourismNoteGoods::query()->where('note_id', $noteId)->delete();
    }
}
