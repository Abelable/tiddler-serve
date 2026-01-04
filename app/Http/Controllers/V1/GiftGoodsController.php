<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Mall\Goods\GiftGoods;
use App\Services\Mall\Goods\GiftGoodsService;
use App\Services\Mall\Goods\GiftTypeService;
use App\Services\Mall\Goods\GoodsService;
use App\Services\Mall\Goods\ShopService;
use App\Utils\Inputs\GiftGoodsPageInput;
use Illuminate\Support\Facades\Cache;

class GiftGoodsController extends Controller
{
    protected $only = [];

    public function typeOptions()
    {
        $cacheKey = 'gift_type_options';
        $options = Cache::remember($cacheKey, 1440, function () {
            return GiftTypeService::getInstance()->getTypeOptions(['id', 'name']);
        });
        return $this->success($options);
    }

    public function list()
    {
        /** @var GiftGoodsPageInput $input */
        $input = GiftGoodsPageInput::new();

        $typeId = $input->typeId ?: 0;

        if ($input->page == 1) {
            $cacheKey = 'gift_goods_type_' . $typeId;
            $result = Cache::remember($cacheKey, 1440, function () use ($input) {
                return $this->giftGoodsPage($input);
            });
        } else {
            $result = $this->giftGoodsPage($input);
        }

        return $this->success($result);
    }

    private function giftGoodsPage(GiftGoodsPageInput $input)
    {
        $page = GiftGoodsService::getInstance()->getPage($input);
        $giftGoodsList = collect($page->items());

        if ($giftGoodsList->isEmpty()) {
            return $this->paginate($page, []);
        }

        $goodsIds = $giftGoodsList->pluck('goods_id')->toArray();
        $columns = ['id', 'shop_id', 'cover', 'name', 'price', 'market_price', 'sales_volume'];

        $goodsList = GoodsService::getInstance()
            ->getGoodsListByIds($goodsIds, $columns)
            ->keyBy('id');

        $shopIds = $goodsList->pluck('shop_id')->unique()->toArray();
        $shopList = ShopService::getInstance()
            ->getShopListByIds($shopIds, ['id', 'logo', 'name'])
            ->keyBy('id');

        $list = $giftGoodsList->map(function (GiftGoods $giftGoods) use ($shopList, $goodsList) {
            $goods = $goodsList->get($giftGoods->goods_id);

            if (!$goods) {
                return null;
            }

            $goods['shopInfo'] = $shopList->get($goods->shop_id);
            unset($goods['shop_id']);

            $goods['isGift'] = 1;
            $goods['giftDuration'] = $giftGoods->duration;

            return $goods;
        })->filter()->values();

        return $this->paginate($page, $list);
    }
}
