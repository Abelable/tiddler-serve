<?php

namespace App\Services;

use App\Models\Cart;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CartAddInput;
use App\Utils\Inputs\CartEditInput;

class CartService extends BaseService
{
    public function cartGoodsNumber($userId)
    {
        return Cart::query()->where('user_id', $userId)->where('scene', '1')->sum('number');
    }

    public function cartList($userId, $columns = ['*'])
    {
        return Cart::query()->where('user_id', $userId)->where('scene', '1')->get($columns);
    }

    public function addCart($userId, CartAddInput $input, $scene = 1)
    {
        $goodsId = $input->goodsId;
        $selectedSkuIndex = $input->selectedSkuIndex;
        $number = $input->number;

        [$goods, $skuList] = $this->validateCartGoodsStatus($goodsId, $selectedSkuIndex, $number);

        $cart = $this->getExistCart($goodsId, $selectedSkuIndex, $scene);
        if (!is_null($cart)) {
            $cart->number = $cart->number + $number;
        } else {
            $cart = Cart::new();
            $cart->scene = $scene;
            $cart->user_id = $userId;
            $cart->goods_id = $goodsId;
            $cart->shop_id = $goods->shop_id;
            $cart->goods_category_id = $goods->category_id;
            $cart->goods_image = $goods->image;
            $cart->goods_name = $goods->name;
            if (count($skuList) != 0 && $selectedSkuIndex != -1 ) {
                $cart->selected_sku_index = $selectedSkuIndex;
                $cart->selected_sku_name = $skuList[$selectedSkuIndex]->name;
                $cart->price = $skuList[$selectedSkuIndex]->price;
            } else {
                $cart->price = $goods->price;
            }
            $cart->market_price = $goods->market_price;
            $cart->number = $number;
        }
        $cart->save();

        return $cart;
    }

    public function editCart(CartEditInput $input)
    {
        $cartId = $input->id;
        $goodsId = $input->goodsId;
        $selectedSkuIndex = $input->selectedSkuIndex;
        $number = $input->number;

        $cart = $this->getExistCart($goodsId, $selectedSkuIndex, 1, $cartId);
        if (!is_null($cart)) {
            $this->throwBusinessException(CodeResponse::DATA_EXISTED, '购物车中已存在当前规格商品');
        }

        [$goods, $skuList] = $this->validateCartGoodsStatus($goodsId, $selectedSkuIndex, $number);

        $cart = $this->getCartById($cartId);
        if (is_null($cart)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '购物车中未添加该商品');
        }
        if ($cart->status == 3) {
            $this->throwBusinessException(CodeResponse::CART_INVALID_OPERATION, '购物车商品已下架，无法编辑');
        }

        if (count($skuList) != 0 && $selectedSkuIndex != -1) {
            $cart->selected_sku_index = $selectedSkuIndex;
            $cart->selected_sku_name = $skuList[$selectedSkuIndex]->name;
            $cart->price = $skuList[$selectedSkuIndex]->price;
        }

        $cart->number = $number;
        if ($cart->status == 2) {
            $cart->status = 1;
            $cart->status_desc = '';
        }
        $cart->save();
        $cart['stock'] = (count($skuList) != 0 && $selectedSkuIndex != -1) ? $skuList[$selectedSkuIndex]->stock : $goods->stock;

        return $cart;
    }

    public function validateCartGoodsStatus($goodsId, $selectedSkuIndex, $number)
    {
        $goods = GoodsService::getInstance()->getGoodsById($goodsId);
        $skuList = json_decode($goods->sku_list);

        if (is_null($goods) || $goods->status != 1) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        if (count($skuList) != 0 && $selectedSkuIndex != -1) {
            $stock = $skuList[$selectedSkuIndex]->stock;
            if ($stock == 0 || $number > $stock) {
                $this->throwBusinessException(CodeResponse::CART_INVALID_OPERATION, '所选规格库存不足');
            }
        }
        if ($goods->stock == 0 || $number > $goods->stock) {
            $this->throwBusinessException(CodeResponse::CART_INVALID_OPERATION, '商品库存不足');
        }

        return [$goods, $skuList];
    }

    public function getExistCart($goodsId, $selectedSkuIndex, $scene, $id = 0, $columns = ['*'])
    {
        $query = Cart::query();
        if ($id != 0) {
            $query = $query->where('id', '!=', $id);
        }
        return $query
            ->where('goods_id', $goodsId)
            ->where('selected_sku_index', $selectedSkuIndex)
            ->where('scene', $scene)
            ->first($columns);
    }

    public function getCartById($id, $columns = ['*'])
    {
        return Cart::query()->find($id, $columns);
    }

    public function deleteCartList($userId, array $ids)
    {
        return Cart::query()->where('user_id', $userId)->whereIn('id', $ids)->delete();
    }
}
