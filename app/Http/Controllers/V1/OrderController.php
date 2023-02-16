<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Goods;
use App\Models\Order;
use App\Models\Shop;
use App\Services\AddressService;
use App\Services\CartService;
use App\Services\GoodsService;
use App\Services\OrderService;
use App\Services\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Enums\OrderEnums;
use App\Utils\Inputs\CreateOrderInput;
use Illuminate\Support\Facades\Cache;
use function Sodium\add;

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
        $cartList = CartService::getInstance()->getCartListByIds($this->userId(), $cartIds, $cartListColumns);

        $freightPrice = 0;
        $totalPrice = 0;
        $totalNumber = 0;
        foreach ($cartList as $cart) {
            $price = bcmul($cart->price, $cart->number, 2);
            $totalPrice = bcadd($totalPrice, $price, 2);
            $totalNumber = $totalNumber + $cart->number;
            // todo 计算运费
        }
        $paymentAmount = bcadd($totalPrice, $freightPrice, 2);

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
            'totalPrice' => $totalPrice,
            'totalNumber' => $totalNumber,
            'paymentAmount' => $paymentAmount
        ]);
    }

    public function submit()
    {
        /** @var CreateOrderInput $input */
        $input = CreateOrderInput::new();

        // 分布式锁，防止重复请求
        $lockKey = sprintf('create_order_%s_%s', $this->userId(), md5(serialize($input)));
        $lock = Cache::lock($lockKey, 5);
        if (!$lock->get()) {
            $this->fail(CodeResponse::FAIL, '请勿重复请求');
        }

        // 1.获取地址
        $address = AddressService::getInstance()->getById($this->userId(), $input->addressId);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '用户地址不存在');
        }

        // 2.获取购物车商品
        $cartList = CartService::getInstance()->getCartListByIds($this->userId(), $input->cartIds);
        $goodsIds = array_unique($cartList->pluck('goods_id')->toArray());
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds)->keyBy('id');

        // 3.按商家进行订单拆分，生成对应订单
        $shopIds = array_unique($cartList->pluck('shop_id')->toArray());
        $shopList = ShopService::getInstance()->getShopListByIds($shopIds);

        $orderIds = $shopList->map(function (Shop $shop) use ($goodsList, $address, $cartList) {
            $goodsPrice = 0;
            $freightPrice = 0;
            $orderGoodsList = $cartList->filter(function (Cart $cart) use ($shop) {
                return $cart->shop_id == $shop->id;
            })->map(function (Cart $cart) use (&$goodsPrice, $goodsList) {
                /** @var Goods $goods */
                $goods = $goodsList->get($cart->goods_id);
                if (is_null($goods) || $goods->status != 1) {
                    return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
                }

                $skuList = json_decode($goods->sku_list);
                $number = $cart->number;
                $selectedSkuIndex  = $cart->selected_sku_index;

                if (count($skuList) != 0 && $selectedSkuIndex != -1) {
                    $stock = $skuList[$selectedSkuIndex]->stock;
                    if ($stock == 0 || $number > $stock) {
                        return $this->fail(CodeResponse::CART_INVALID_OPERATION, '所选规格库存不足');
                    }
                    // 减规格库存
                    $skuList[$selectedSkuIndex]->stock = $skuList[$selectedSkuIndex]->stock - $number;
                    $goods->sku_list = json_encode($skuList);
                } else {
                    if ($goods->stock == 0 || $number > $goods->stock) {
                        return $this->fail(CodeResponse::CART_INVALID_OPERATION, '商品库存不足');
                    }
                }

                // 商品减库存
                $goods->stock = $goods->stock - $number;
                $goods->save();

                $price = bcmul($cart->price, $cart->number, 2);
                $goodsPrice = bcadd($goodsPrice, $price, 2);
                // todo 计算运费

                return [
                    'image' => $cart->goods_image,
                    'name' => $cart->goods_name,
                    'selected_sku_name' => $cart->selected_sku_name,
                    'price' => $cart->price,
                    'number' => $cart->number,
                ];
            });
            $order = Order::new();
            $order->order_sn = OrderService::getInstance()->generateOrderSn();
            $order->status = OrderEnums::STATUS_CREATE;
            $order->user_id = $this->userId();
            $order->consignee = $address->name;
            $order->mobile = $address->mobile;
            $order->address = $address->region_desc . ' ' . $address->address_detail;
            $order->shop_id = $shop->id;
            $order->shop_avatar = $shop->avatar;
            $order->shop_name = $shop->name;
            $order->goods_list = json_encode($orderGoodsList);
            $order->goods_price = $goodsPrice;
            $order->freight_price = $freightPrice;
            $order->payment_amount = bcadd($goodsPrice, $freightPrice, 2);
            $order->refund_amount = bcadd($goodsPrice, $freightPrice, 2);
            $order->save();
            return $order->id;
        });
        if (in_array(0, $shopIds)) {
            $goodsPrice = 0;
            $freightPrice = 0;
            $orderGoodsList = $cartList->filter(function (Cart $cart) {
                return $cart->shop_id == 0;
            })->map(function (Cart $cart) use (&$goodsPrice, $goodsList) {
                $number = $cart->number;
                $selectedSkuIndex  = $cart->selected_sku_index;
                /** @var Goods $goods */
                $goods = $goodsList->get($cart->goods_id);
                $skuList = json_decode($goods->sku_list);

                if (is_null($goods) || $goods->status != 1) {
                    return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
                }
                if (count($skuList) != 0 && $selectedSkuIndex != -1) {
                    $stock = $skuList[$selectedSkuIndex]->stock;
                    if ($stock == 0 || $number > $stock) {
                        return $this->fail(CodeResponse::CART_INVALID_OPERATION, '所选规格库存不足');
                    }
                    // 减规格库存
                    $skuList[$selectedSkuIndex]->stock = $skuList[$selectedSkuIndex]->stock - $number;
                    $goods->sku_list = json_encode($skuList);
                } else {
                    if ($goods->stock == 0 || $number > $goods->stock) {
                        return $this->fail(CodeResponse::CART_INVALID_OPERATION, '商品库存不足');
                    }
                }

                // 商品减库存
                $goods->stock = $goods->stock - $number;
                $goods->save();

                $price = bcmul($cart->price, $cart->number, 2);
                $goodsPrice = bcadd($goodsPrice, $price, 2);
                // todo 计算运费

                return [
                    'image' => $cart->goods_image,
                    'name' => $cart->goods_name,
                    'selected_sku_name' => $cart->selected_sku_name,
                    'price' => $cart->price,
                    'number' => $cart->number,
                ];
            });
            $order = Order::new();
            $order->order_sn = OrderService::getInstance()->generateOrderSn();
            $order->status = OrderEnums::STATUS_CREATE;
            $order->user_id = $this->userId();
            $order->consignee = $address->name;
            $order->mobile = $address->mobile;
            $order->address = $address->region_desc . ' ' . $address->address_detail;
            $order->shop_name = '官方自营';
            $order->goods_list = json_encode($orderGoodsList);
            $order->goods_price = $goodsPrice;
            $order->freight_price = $freightPrice;
            $order->payment_amount = bcadd($goodsPrice, $freightPrice, 2);
            $order->refund_amount = bcadd($goodsPrice, $freightPrice, 2);
            $order->save();
            $orderIds->push($order->id);
        }

        // 4.清空购物车
        CartService::getInstance()->deleteCartList($this->userId(), $input->cartIds);

        // todo 5.设置订单支付超时任务

    }
}
