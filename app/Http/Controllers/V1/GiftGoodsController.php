<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\GiftGoods;
use App\Services\GiftGoodsService;
use App\Services\GoodsService;

class GiftGoodsController extends Controller
{
    protected $only = [];

    public function list()
    {
        $type = $this->verifyRequiredInteger('type');

        $page = GiftGoodsService::getInstance()->getGoodsPage($type);
        $giftGoodsList = collect($page->items());

        $goodsIds = $giftGoodsList->pluck('goods_id')->toArray();
        $columns = ['id', 'cover', 'name', 'price', 'market_price', 'sales_volume'];
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds, $columns)->keyBy('id');

        $list = $giftGoodsList->map(function (GiftGoods $giftGoods) use ($goodsList) {
            $goods = $goodsList->get($giftGoods->goods_id);
            $goods['isGift'] = 1;
            return $goods;
        });

        return $this->success($list);
    }
}
