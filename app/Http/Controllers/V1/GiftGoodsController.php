<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Mall\Goods\GiftGoods;
use App\Models\Mall\Goods\Goods;
use App\Services\Mall\Goods\GiftGoodsService;
use App\Services\Mall\Goods\GiftTypeService;
use App\Services\Mall\Goods\GoodsService;
use App\Services\Mall\Goods\ShopService;
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

        $page = GiftGoodsService::getInstance()->getPage($input);
        $giftGoodsList = collect($page->items());

        $goodsIds = $giftGoodsList->pluck('goods_id')->toArray();
        $columns = ['id', 'shop_id', 'cover', 'name', 'price', 'market_price', 'sales_volume'];
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds, $columns)->keyBy('id');

        $shopIds = $goodsList->pluck('shop_id')->toArray();
        $shopList = ShopService::getInstance()->getShopListByIds($shopIds, ['id', 'logo', 'name'])->keyBy('id');

        $list = $giftGoodsList->map(function (GiftGoods $giftGoods) use ($shopList, $goodsList) {
            /** @var Goods $goods */
            $goods = $goodsList->get($giftGoods->goods_id);

            $shopInfo = $shopList->get($goods->shop_id);
            $goods['shopInfo'] = $shopInfo;
            unset($goods['shop_id']);

            $goods['isGift'] = 1;
            $goods['giftDuration'] = $giftGoods->duration;

            return $goods;
        });

        return $this->success($this->paginate($page, $list));
    }
}
