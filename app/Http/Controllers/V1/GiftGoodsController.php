<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\GiftGoodsService;
use App\Services\GoodsService;

class GiftGoodsController extends Controller
{
    protected $only = [];

    public function list()
    {
        $type = $this->verifyRequiredInteger('type');
        $columns = ['id', 'cover', 'name', 'price', 'market_price', 'sales_volume'];

        $goodsIds = GiftGoodsService::getInstance()->getGoodsList([$type])->pluck('goods_id')->toArray();
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds, $columns);
        $list = $goodsList->map(function ($goods) {
            $goods['isGift'] = 1;
            return $goods;
        });

        // todo 商品列表存缓存

        return $this->success($list);
    }
}
