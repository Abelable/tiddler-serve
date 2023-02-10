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
        $cartColumns = [
            'id',
            'status',
            'status_desc',
            'goods_id',
            'shop_id',
            'goods_category_id',
            'goods_image',
            'goods_name',
            'selected_sku_name',
            'selected_sku_index',
            'price',
            'market_price',
            'number'
        ];
        $list = CartService::getInstance()->cartList($this->userId(), $cartColumns);
        $goodsIds = array_unique($list->pluck('goods_id')->toArray());
        $goodsCategoryIds = array_unique($list->pluck('goods_category_id')->toArray());
        $shopIds = array_unique($list->pluck('shop_id')->toArray());

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds)->keyBy('id');
        $cartGoodsList = $list->map(function (Cart $cart) use ($goodsList) {
            /** @var Goods $goods */
            $goods = $goodsList->get($cart->goods_id);
            if (is_null($goods) || $goods->status != 1) {
                $cart->status = 3;
                $cart->status_desc = '商品已下架';
                $cart->save();
                return $cart;
            }
            if ($cart->selected_sku_index == -1) {
                if ($cart->number > $goods->stock) {
                    if ($goods->stock != 0) {
                        $cart->number = $goods->stock;
                        $cart->save();
                        $cart['stock'] = $goods->stock;
                    } else {
                        $cart->status = 3;
                        $cart->status_desc = '商品暂无库存';
                        $cart->save();
                    }
                } else {
                    $cart['stock'] = $goods->stock;
                }
                return $cart;
            }
            if ($cart->selected_sku_index != -1) {
                $skuList = json_decode($goods->sku_list);
                $sku = $skuList[$cart->selected_sku_index];
                if (is_null($sku) || $cart->selected_sku_name != $sku->name) {
                    $cart->status = 2;
                    $cart->status_desc = '商品规格不存在';
                    $cart->save();
                    return $cart;
                }
                if ($cart->number > $sku->stock) {
                    if ($sku->stock != 0) {
                        $cart->number = $sku->stock;
                        $cart->save();
                        $cart['stock'] = $sku->stock;
                    } else {
                        $cart->status = 2;
                        $cart->status_desc = '当前规格暂无库存';
                    }
                } else {
                    $cart['stock'] = $sku->stock;
                }
                return $cart;
            }
        });

        $shopList = ShopService::getInstance()->getShopListByIds($shopIds, ['id', 'avatar', 'name']);
        $cartList = $shopList->map(function (Shop $shop) use ($cartGoodsList) {
            return [
                'shopInfo' => $shop,
                'goodsList' => $cartGoodsList->filter(function (Cart $cart) use ($shop) {
                    return $cart->shop_id == $shop->id;
                })
            ];
        });
        if (in_array(0, $shopIds)) {
            $cartList->prepend([
                'goodsList' => $cartGoodsList->filter(function (Cart $cart) {
                    return $cart->shop_id == 0;
                })
            ]);
        }

        $recommendGoodsList = GoodsService::getInstance()->getRecommendGoodsList($goodsIds, $goodsCategoryIds);

        return $this->success([
            'cartList' => $cartList,
            'recommendGoodsList' => $recommendGoodsList
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

        return $this->goodsNumber();
    }

    public function edit()
    {
        /** @var CartEditInput $input */
        $input = CartEditInput::new();
        $selectedSkuIndex = $input->selectedSkuIndex;
        $number = $input->number;

        $cart = CartService::getInstance()->getExistCart($input->goodsId, $selectedSkuIndex, $input->id);
        if (!is_null($cart)) {
            return $this->fail(CodeResponse::DATA_EXISTED, '购物车中已存在当前规格商品');
        }

        $this->validateCartGoodsStatus($input->goodsId, $selectedSkuIndex, $number);

        $cart = CartService::getInstance()->getCartById($input->id);
        if (is_null($cart)) {
            return $this->fail(CodeResponse::NOT_FOUND, '购物车中未添加该商品');
        }
        if ($cart->status != 1) {
            return $this->fail(CodeResponse::CART_INVALID_OPERATION, '购物车商品已失效，无法编辑');
        }

        $cart->selected_sku_index = $selectedSkuIndex;
        $cart->number = $number;
        $cart->save();

        return $this->list();
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

    public function delete()
    {
        $ids = $this->verifyArrayNotEmpty('ids', []);
        CartService::getInstance()->deleteCartList($this->userId(), $ids);
        return $this->list();
    }
}
