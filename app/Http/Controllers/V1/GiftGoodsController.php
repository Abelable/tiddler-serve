<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\GiftGoods;
use App\Services\GiftGoodsService;
use App\Services\GiftTypeService;
use App\Services\GoodsService;
use App\Utils\Inputs\GiftGoodsPageInput;

class GiftGoodsController extends Controller
{
    protected $only = [];

    public function typeOptions()
    {
        $options = GiftTypeService::getInstance()->getTypeOptions(['id', 'name']);
        return $this->success($options);
    }

    public function list()
    {
        /** @var GiftGoodsPageInput $input */
        $input = GiftGoodsPageInput::new();

        $page = GiftGoodsService::getInstance()->getGoodsPage($input);
        $giftGoodsList = collect($page->items());

        $goodsIds = $giftGoodsList->pluck('goods_id')->toArray();
        $columns = ['id', 'cover', 'name', 'price', 'market_price', 'sales_volume'];
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds, $columns)->keyBy('id');

        $list = $giftGoodsList->map(function (GiftGoods $giftGoods) use ($goodsList) {
            $goods = $goodsList->get($giftGoods->goods_id);
            $goods['isGift'] = 1;
            return $goods;
        });

        return $this->success($this->paginate($page, $list));
    }
}
