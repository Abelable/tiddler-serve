<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Shop;
use App\Services\AddressService;
use App\Services\CartService;
use App\Services\OrderGoodsService;
use App\Services\OrderService;
use App\Services\ShopService;
use App\Services\TicketSpecService;
use App\Utils\CodeResponse;
use App\Utils\Enums\OrderEnums;
use App\Utils\Inputs\CreateScenicOrderInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yansongda\LaravelPay\Facades\Pay;

class ScenicOrderController extends Controller
{
    public function paymentAmount()
    {
        $ticketId = $this->verifyRequiredId('ticketId');
        $categoryId = $this->verifyRequiredId('categoryId');
        $timeStamp = $this->verifyRequiredInteger('timeStamp');
        $num = $this->verifyRequiredInteger('num');

        $priceList = TicketSpecService::getInstance()->getPriceList($ticketId, $categoryId);
        $priceUnit = array_filter($priceList, function ($item) use ($timeStamp) {
                return $timeStamp >= $item->startDate && $timeStamp <= $item->endDate;
            })[0] ?? null;
        if (is_null($priceUnit)) {
            return $this->fail(CodeResponse::NOT_FOUND, '所选日期暂无门票销售，请更换日期');
        }

        $paymentAmount = (float)bcmul($priceUnit->price, $num, 2);

        return $this->success($paymentAmount);
    }

    public function submit()
    {
        /** @var CreateScenicOrderInput $input */
        $input = CreateScenicOrderInput::new();

        // 分布式锁，防止重复请求
        $lockKey = sprintf('create_scenic_order_%s_%s', $this->userId(), md5(serialize($input)));
        $lock = Cache::lock($lockKey, 5);
        if (!$lock->get()) {
            $this->fail(CodeResponse::FAIL, '请勿重复提交订单');
        }

        $orderIds = DB::transaction(function () use ($input) {
            // 1.获取地址
            $address = AddressService::getInstance()->getById($this->userId(), $input->addressId);
            if (is_null($address)) {
                return $this->fail(CodeResponse::NOT_FOUND, '用户地址不存在');
            }

            // 2.获取购物车商品
            $cartList = CartService::getInstance()->getCartListByIds($this->userId(), $input->cartIds);

            // 3.按商家进行订单拆分，生成对应订单
            $shopIds = array_unique($cartList->pluck('shop_id')->toArray());
            $shopList = ShopService::getInstance()->getShopListByIds($shopIds);

            $orderIds = $shopList->map(function (Shop $shop) use ($address, $cartList) {
                $filterCartList = $cartList->filter(function (Cart $cart) use ($shop) {
                    return $cart->shop_id == $shop->id;
                });
                return OrderService::getInstance()->createOrder($this->userId(), $filterCartList, $address, $shop);
            });
            if (in_array(0, $shopIds)) {
                $filterCartList = $cartList->filter(function (Cart $cart) {
                    return $cart->shop_id == 0;
                });
                $orderId = OrderService::getInstance()->createOrder($this->userId(), $filterCartList, $address);
                $orderIds->push($orderId);
            }

            // 4.清空购物车
            CartService::getInstance()->deleteCartList($this->userId(), $input->cartIds);

            return $orderIds;
        });

        return $this->success($orderIds);
    }

    public function payParams()
    {
        $orderIds = $this->verifyArrayNotEmpty('orderIds');
        $order = OrderService::getInstance()->createWxPayOrder($this->userId(), $orderIds, $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($order);
        return $this->success($payParams);
    }

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');
        $shopId = $this->verifyId('shopId');

        switch ($status) {
            case 1:
                $statusList = [OrderEnums::STATUS_CREATE];
                break;
            case 2:
                $statusList = [OrderEnums::STATUS_PAY];
                break;
            case 3:
                $statusList = [OrderEnums::STATUS_SHIP];
                break;
            case 4:
                $statusList = [OrderEnums::STATUS_CONFIRM, OrderEnums::STATUS_AUTO_CONFIRM];
                break;
            case 5:
                $statusList = [OrderEnums::STATUS_REFUND, OrderEnums::STATUS_REFUND_CONFIRM];
                break;
            default:
                $statusList = [];
                break;
        }

        if ($shopId) {
            $page = OrderService::getInstance()->getShopOrderList($shopId, $statusList, $input);
        } else {
            $page = OrderService::getInstance()->getOrderListByStatus($this->userId(), $statusList, $input);
        }

        $orderList = collect($page->items());

        $orderIds = $orderList->pluck('id')->toArray();
        $goodsListColumns = ['order_id', 'goods_id', 'image', 'name', 'selected_sku_name', 'price', 'number'];
        $groupedGoodsList = OrderGoodsService::getInstance()->getListByOrderIds($orderIds, $goodsListColumns)->groupBy('order_id');
        $list = $orderList->map(function (Order $order) use ($groupedGoodsList) {
            $goodsList = $groupedGoodsList->get($order->id);
            return [
                'id' => $order->id,
                'status' => $order->status,
                'statusDesc' => OrderEnums::STATUS_TEXT_MAP[$order->status],
                'shopId' => $order->shop_id,
                'shopAvatar' => $order->shop_avatar,
                'shopName' => $order->shop_name,
                'goodsList' => $goodsList,
                'paymentAmount' => $order->payment_amount,
                'consignee' => $order->consignee,
                'mobile' => $order->mobile,
                'address' => $order->address,
                'orderSn' => $order->order_sn
            ];
        });

        return $this->success($this->paginate($page, $list));
    }

    public function cancel()
    {
        $id = $this->verifyRequiredId('id');
        OrderService::getInstance()->userCancel($this->userId(), $id);
        return $this->success();
    }

    public function confirm()
    {
        $id = $this->verifyRequiredId('id');
        OrderService::getInstance()->confirm($this->userId(), $id);
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        DB::transaction(function () use ($id) {
            OrderService::getInstance()->delete($this->userId(), $id);
        });
        return $this->success();
    }

    public function refund()
    {
        $id = $this->verifyRequiredId('id');
        OrderService::getInstance()->refund($this->userId(), $id);
        return $this->success();
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $columns = [
            'id',
            'order_sn',
            'status',
            'remarks',
            'consignee',
            'mobile',
            'address',
            'shop_id',
            'shop_avatar',
            'shop_name',
            'goods_price',
            'freight_price',
            'payment_amount',
            'pay_time',
            'ship_time',
            'confirm_time',
            'created_at',
            'updated_at',
        ];
        $order = OrderService::getInstance()->getOrderById($this->userId(), $id, $columns);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }
        $goodsList = OrderGoodsService::getInstance()->getListByOrderId($order->id);
        $order['goods_list'] = $goodsList;
        return $this->success($order);
    }
}
