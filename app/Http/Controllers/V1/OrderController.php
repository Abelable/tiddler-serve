<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Shop;
use App\Services\AddressService;
use App\Services\CartService;
use App\Services\ShopService;

class OrderController extends Controller
{
    public function preOrderInfo()
    {
        $addressId = $this->verifyId('addressId');
        $cartIds = json_decode($this->verifyRequiredString('cartIds'));

        $addressColumns = ['name', 'mobile', 'region_code_list', 'region_desc', 'address_detail'];
        if (is_null($addressId)) {
            $address = AddressService::getInstance()->getDefautlAddress($this->userId(), $addressColumns);
        } else {
            $address = AddressService::getInstance()->getById($this->userId(), $addressId, $addressColumns);
        }

        $cartListColumns = ['shop_id', 'goods_image', 'goods_name', 'selected_sku_name', 'price', 'number'];
        $cartList = CartService::getInstance()->getCartListByIds($cartIds, $cartListColumns);

        $freightPrice = 0;
        $totalPrice = 0;
        foreach ($cartList as $cart) {
            $price = bcmul($cart->price, $cart->number, 2);
            $totalPrice = bcadd($totalPrice, $price, 2);
            // todo 计算运费
        }

        $shopIds = array_unique($cartList->pluck('shop_id')->toArray());
        $shopList = ShopService::getInstance()->getShopListByIds($shopIds, ['id', 'avatar', 'name']);
        $goodsLists = $shopList->map(function (Shop $shop) use ($cartList) {
            return [
                'shopInfo' => $shop,
                'goodsList' => $cartList->filter(function (Cart $cart) use ($shop) {
                    return $cart->shop_id == $shop->id;
                })->map(function (Cart $cart) {
                    unset($cart->shop_id);
                    return $cart;
                })
            ];
        });
        if (in_array(0, $shopIds)) {
            $goodsLists->prepend([
                'goodsList' => $cartList->filter(function (Cart $cart) {
                    return $cart->shop_id == 0;
                })->map(function (Cart $cart) {
                    unset($cart->shop_id);
                    return $cart;
                })
            ]);
        }

        return $this->success([
            'addressInfo' => $address,
            'goodsLists' => $goodsLists,
            'freightPrice' => $freightPrice,
            'totalPrice' => $totalPrice
        ]);
    }
}
