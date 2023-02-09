<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Services\CartService;
use App\Services\GoodsService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CartAddInput;

class CartController extends Controller
{
    public function add()
    {
        /** @var CartAddInput $input */
        $input = CartAddInput::new();
        $selectedSkuIndex = $input->selectedSkuIndex;
        $number = $input->number;

        $goods = GoodsService::getInstance()->getGoodsById($input->goodsId);
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

        $cart = Cart::new();
        $cart->user_id = $this->userId();
        $cart->shop_id = $goods->shop_id;
        $cart->goods_id = $goods->id;
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
        $cart->save();

        return  $this->goodsNumber();
    }

    public function goodsNumber()
    {
        $number = CartService::getInstance()->cartGoodsNumber($this->userId());
        return $this->success($number);
    }
}
