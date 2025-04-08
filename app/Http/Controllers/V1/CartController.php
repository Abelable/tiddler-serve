<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\CartGoods;
use App\Models\Goods;
use App\Models\Shop;
use App\Services\CartGoodsService;
use App\Services\GoodsService;
use App\Services\ShopService;
use App\Utils\Inputs\CartGoodsInput;
use App\Utils\Inputs\CartGoodsEditInput;

class CartController extends Controller
{
    public function goodsNumber()
    {
        $number = CartGoodsService::getInstance()->cartGoodsNumber($this->userId());
        return $this->success((int) $number);
    }

    public function list()
    {
        $cartGoodsColumns = [
            'id',
            'status',
            'status_desc',
            'goods_id',
            'shop_id',
            'category_id',
            'freight_template_id',
            'cover',
            'name',
            'selected_sku_name',
            'selected_sku_index',
            'price',
            'market_price',
            'number'
        ];
        $list = CartGoodsService::getInstance()->cartGoodsList($this->userId(), $cartGoodsColumns);
        $goodsIds = array_unique($list->pluck('goods_id')->toArray());
        $goodsCategoryIds = array_unique($list->pluck('goods_category_id')->toArray());
        $shopIds = array_unique($list->pluck('shop_id')->toArray());

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds)->keyBy('id');
        $cartGoodsList = $list->map(function (CartGoods $cartGoods) use ($goodsList) {
            /** @var Goods $goods */
            $goods = $goodsList->get($cartGoods->goods_id);
            if (is_null($goods) || $goods->status != 1) {
                $cartGoods->status = 3;
                $cartGoods->status_desc = '商品已下架';
                $cartGoods->save();
                return $cartGoods;
            }
            $skuList = json_decode($goods->sku_list);
            if (count($skuList) == 0) {
                if ($cartGoods->number > $goods->stock) {
                    if ($goods->stock != 0) {
                        $cartGoods->number = $goods->stock;
                        $cartGoods->save();
                        $cartGoods['stock'] = $goods->stock;
                    } else {
                        $cartGoods->status = 3;
                        $cartGoods->status_desc = '商品暂无库存';
                        $cartGoods->save();
                    }
                } else {
                    $cartGoods['stock'] = $goods->stock;
                }
                return $cartGoods;
            }
            $sku = $skuList[$cartGoods->selected_sku_index];
            if (is_null($sku) || $cartGoods->selected_sku_name != $sku->name) {
                $cartGoods->status = 2;
                $cartGoods->status_desc = '商品规格不存在';
                $cartGoods->selected_sku_index = -1;
                $cartGoods->selected_sku_name = '';
                $cartGoods->save();
                return $cartGoods;
            }
            if ($cartGoods->number > $sku->stock) {
                if ($sku->stock != 0) {
                    $cartGoods->number = $sku->stock;
                    $cartGoods->save();
                    $cartGoods['stock'] = $sku->stock;
                } else {
                    $cartGoods->status = 2;
                    $cartGoods->status_desc = '当前规格暂无库存';
                    $cartGoods->selected_sku_index = -1;
                    $cartGoods->selected_sku_name = '';
                    $cartGoods->save();
                }
            } else {
                $cartGoods['stock'] = $sku->stock;
            }
            return $cartGoods;
        });

        $shopList = ShopService::getInstance()->getShopListByIds($shopIds, ['id', 'avatar', 'name']);
        $cartList = $shopList->map(function (Shop $shop) use ($cartGoodsList) {
            return [
                'shopInfo' => $shop,
                'goodsList' => $cartGoodsList->filter(function (CartGoods $cartGoods) use ($shop) {
                    return $cartGoods->shop_id == $shop->id;
                })->map(function (CartGoods $cartGoods) {
                    unset($cartGoods->shop_id);
                    unset($cartGoods->goods_category_id);
                    return $cartGoods;
                })
            ];
        });
        if (in_array(0, $shopIds)) {
            $cartList->prepend([
                'goodsList' => $cartGoodsList->filter(function (CartGoods $cartGoods) {
                    return $cartGoods->shop_id == 0;
                })->map(function (CartGoods $cartGoods) {
                    unset($cartGoods->shop_id);
                    unset($cartGoods->goods_category_id);
                    return $cartGoods;
                })
            ]);
        }

        $recommendGoodsList = GoodsService::getInstance()->getRecommendGoodsList($goodsIds, $goodsCategoryIds);

        return $this->success([
            'cartList' => $cartList,
            'recommendGoodsList' => $recommendGoodsList
        ]);
    }

    public function fastAdd()
    {
        /** @var CartGoodsInput $input */
        $input = CartGoodsInput::new();
        $cartGoods = CartGoodsService::getInstance()->addCartGoods($this->userId(), $input, 2);
        return $this->success($cartGoods->id);
    }

    public function add()
    {
        /** @var CartGoodsInput $input */
        $input = CartGoodsInput::new();
        CartGoodsService::getInstance()->addCartGoods($this->userId(), $input);
        return $this->goodsNumber();
    }

    public function edit()
    {
        /** @var CartGoodsEditInput $input */
        $input = CartGoodsEditInput::new();
        $cartGoods = CartGoodsService::getInstance()->editCartGoods($this->userId(), $input);
        return $this->success([
            'status' => $cartGoods->status,
            'statusDesc' => $cartGoods->status_desc,
            'selectedSkuIndex' => $cartGoods->selected_sku_index,
            'selectedSkuName' => $cartGoods->selected_sku_name,
            'price' => $cartGoods->price,
            'marketPrice' => $cartGoods->market_price,
            'number' => $cartGoods->number,
            'numberLimit' => $cartGoods['numberLimit'],
        ]);
    }

    public function delete()
    {
        $ids = $this->verifyArrayNotEmpty('ids', []);
        CartGoodsService::getInstance()->deleteCartGoodsList($this->userId(), $ids);
        return $this->success();;
    }
}
