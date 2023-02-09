<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Goods;
use App\Models\Shop;
use App\Services\CartService;
use App\Services\GoodsService;
use App\Services\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CartAddInput;
use App\Utils\Inputs\CartEditInput;

class CartController extends Controller
{
    public function goodsNumber()
    {
        $number = CartService::getInstance()->cartGoodsNumber($this->userId());
        return $this->success($number);
    }

    public function list()
    {
        $cartColumns = ['id', 'goods_id', 'shop_id', 'goods_category_id', 'goods_image', 'goods_name', 'selected_sku_name', 'selected_sku_index', 'price', 'market_price', 'number'];
        $list = CartService::getInstance()->cartList($this->userId(), $cartColumns);
        $goodsIds = array_unique($list->pluck('goods_id')->toArray());
        $goodsCategoryIds = array_unique($list->pluck('goods_category_id')->toArray());
        $shopIds = array_unique($list->pluck('shop_id')->toArray());

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds);
        $cartGoodsList = $list->map(function (Cart $cart) use ($goodsList) {
            /** @var Goods $goods */
            $goods = $goodsList->get($cart->goods_id);
            if (is_null($goods) || $goods->status != 1) {
                $cart->status = 2;
                $cart->status_desc = '商品已下架';
                $cart->save();
            }
            if ($cart->selected_sku_index == -1 && $cart->number > $goods->stock) {
                if ($goods->stock != 0) {
                    $cart->number = $goods->stock;
                } else {
                    $cart->status = 2;
                    $cart->status_desc = '商品库存不足';
                }
                $cart->save();
            }
            if ($cart->selected_sku_index != -1) {
                $skuList = json_decode($goods->sku_list);
                $sku = $skuList[$cart->selected_sku_index];
                if (is_null($sku) || $cart->selected_sku_name != $sku->name) {
                    $cart->status = 2;
                    $cart->status_desc = '商品规格不存在';
                    $cart->save();
                }
                if ($cart->number > $sku->stock) {
                    if ($sku->stock != 0) {
                        $cart->number = $sku->stock;
                        $cart->save();
                    } else {
                        $cart->status = 2;
                        $cart->status_desc = '当前规格库存不足';
                        return $cart;
                    }
                }
            }
            unset($cart->shop_id);
            unset($cart->goods_category_id);
            unset($cart->selected_sku_index);
            return $cart;
        });

        $shopList = ShopService::getInstance()->getShopListByIds($shopIds, ['id', 'avatar', 'name'])->keyBy('id');
        $cartList = $shopList->map(function (Shop $shop) use ($cartGoodsList) {
            return [
                'shop_info' => $shop,
                'goods_list' => array_filter($cartGoodsList, function (Goods $goods) use ($shop) {
                    return $goods->shop_id == $shop->id;
                })
            ];
        });

        $goodsColumns = ['id', 'shop_id', 'image', 'name', 'price', 'market_price', 'sales_volume'];
        $recommendGoodsList = GoodsService::getInstance()->getTopListByCategoryIds($goodsIds, $goodsCategoryIds, 10, $goodsColumns);

        return $this->success([
            'cart_list' => $cartList,
            'recommend_goods_list' => $recommendGoodsList
        ]);
    }

    public function add()
    {
        /** @var CartAddInput $input */
        $input = CartAddInput::new();
        $selectedSkuIndex = $input->selectedSkuIndex;
        $number = $input->number;

        [$goods, $skuList] = $this->validateCartGoodsStatus($input->goodsId, $selectedSkuIndex, $number);

        $cart = CartService::getInstance()->getExistCart($goods->id, $selectedSkuIndex);
        if (!is_null($cart)) {
            $cart->number = $cart->number + $number;
        } else {
            $cart = Cart::new();
            $cart->user_id = $this->userId();
            $cart->shop_id = $goods->shop_id;
            $cart->goods_id = $goods->id;
            $cart->goods_category_id = $goods->category_id;
            $cart->goods_image = $goods->image;
            $cart->goods_name = $goods->name;
            if ($selectedSkuIndex != -1 && count($skuList) != 0) {
                $cart->selected_sku_name = $skuList[$selectedSkuIndex]->name;
                $cart->selected_sku_index = $selectedSkuIndex;
                $cart->price = $skuList[$selectedSkuIndex]->price;
            } else {
                $cart->price = $goods->price;
            }
            $cart->market_price = $goods->market_price;
            $cart->number = $number;
        }
        $cart->save();

        return  $this->goodsNumber();
    }

    public function edit()
    {
        /** @var CartEditInput $input */
        $input = CartEditInput::new();
        $selectedSkuIndex = $input->selectedSkuIndex;
        $number = $input->number;

        $cart = CartService::getInstance()->getExistCart($input->goodsId, $selectedSkuIndex);
        if (!is_null($cart)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '购物车中已存在当前规格商品');
        }

        $this->validateCartGoodsStatus($input->goodsId, $selectedSkuIndex, $number);

        $cart = CartService::getInstance()->getCartById($input->id);
        if ($cart->status != 1) {
            return $this->fail(CodeResponse::CART_INVALID_OPERATION, '购物车商品已失效，无法编辑');
        }

        $cart->selected_sku_index = $selectedSkuIndex;
        $cart->number = $number;
        $cart->save();

        return  $this->goodsNumber();
    }

    private function validateCartGoodsStatus($goodsId, $selectedSkuIndex, $number)
    {
        $goods = GoodsService::getInstance()->getGoodsById($goodsId);
        if (is_null($goods) || $goods->status != 1) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        if ($selectedSkuIndex == -1) {
            if ($goods->stock == 0 || $number > $goods->stock) {
                return $this->fail(CodeResponse::CART_INVALID_OPERATION, '商品库存不足');
            }
        }
        $skuList = json_decode($goods->sku_list);
        if ($selectedSkuIndex != -1) {
            $stock = $skuList[$selectedSkuIndex]->stock;
            if ($stock == 0 || $number > $stock) {
                return $this->fail(CodeResponse::CART_INVALID_OPERATION, '所选规格库存不足');
            }
        }
        return [$goods, $skuList];
    }
}
