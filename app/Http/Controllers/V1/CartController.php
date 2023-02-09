<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\GoodsService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CartAddInput;

class CartController extends Controller
{
    public function add()
    {
        /** @var CartAddInput $input */
        $input = CartAddInput::new();

        $goods = GoodsService::getInstance()->getGoodsById($input->goodsId);
        if (is_null($goods) || $goods->status != 1) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        if ($input->selectedSkuIndex == -1) {
            if ($goods->stock == 0 || $input->number > $goods->stock) {
                return $this->fail(CodeResponse::CART_INVALID_OPERATION, '商品库存不足');
            }
        }
        if ($input->selectedSkuIndex != -1) {
            $skuList = json_decode($goods->sku_list);
            $stock = $skuList[$input->selectedSkuIndex]->stock;
            if ($stock == 0 || $input->number > $stock) {
                return $this->fail(CodeResponse::CART_INVALID_OPERATION, '所选规格库存不足');
            }
        }


    }
}
