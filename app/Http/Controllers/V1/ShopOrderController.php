<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderGoodsService;
use App\Services\OrderPackageGoodsService;
use App\Services\OrderPackageService;
use App\Services\OrderService;
use App\Services\OrderVerifyService;
use App\Services\ShopManagerService;
use App\Services\ShopPickupAddressService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Enums\OrderStatus;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

class ShopOrderController extends Controller
{
    public function total()
    {
        $shopId = $this->verifyRequiredId('shopId');

        return $this->success([
            OrderService::getInstance()->getShopTotal($shopId, $this->statusList(1)),
            OrderService::getInstance()->getShopTotal($shopId, $this->statusList(2)),
            OrderService::getInstance()->getShopTotal($shopId, $this->statusList(3)),
            0,
            OrderService::getInstance()->getShopTotal($shopId, [OrderStatus::REFUNDING]),
        ]);
    }

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');
        $shopId = $this->verifyId('shopId');

        $statusList = $this->statusList($status);
        $page = OrderService::getInstance()->getShopOrderList($shopId, $statusList, $input);
        $orderList = collect($page->items());
        $list = $this->handleOrderList($orderList);

        return $this->success($this->paginate($page, $list));
    }

    private function statusList($status) {
        switch ($status) {
            case 1:
                $statusList = [OrderStatus::PAID, OrderStatus::EXPORTED];
                break;
            case 2:
                $statusList = [OrderStatus::SHIPPED];
                break;
            case 3:
                $statusList = [OrderStatus::PENDING_VERIFICATION];
                break;
            case 4:
                $statusList = [OrderStatus::FINISHED];
                break;
            case 5:
                $statusList = [OrderStatus::REFUNDING, OrderStatus::REFUNDED];
                break;
            default:
                $statusList = [
                    OrderStatus::PAID,
                    OrderStatus::EXPORTED,
                    OrderStatus::REFUNDING,
                    OrderStatus::REFUNDED,
                    OrderStatus::SHIPPED,
                    OrderStatus::PENDING_VERIFICATION,
                    OrderStatus::CONFIRMED,
                    OrderStatus::AUTO_CONFIRMED,
                    OrderStatus::ADMIN_CONFIRMED,
                    OrderStatus::FINISHED,
                    OrderStatus::AUTO_FINISHED
                ];
                break;
        }

        return $statusList;
    }

    private function handleOrderList($orderList)
    {
        $userIds = $orderList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()
            ->getListByIds($userIds, ['id', 'avatar', 'nickname'])
            ->keyBy('id');

        $orderIds = $orderList->pluck('id')->toArray();
        $goodsListColumns = ['order_id', 'goods_id', 'cover', 'name', 'selected_sku_name', 'price', 'number'];
        $groupedGoodsList = OrderGoodsService::getInstance()
            ->getListByOrderIds($orderIds, $goodsListColumns)->groupBy('order_id');

        return $orderList->map(function (Order $order) use ($groupedGoodsList, $userList) {
            $userInfo = $userList->get($order->user_id);
            $goodsList = $groupedGoodsList->get($order->id);

            return [
                'id' => $order->id,
                'status' => $order->status,
                'statusDesc' => OrderStatus::TEXT_MAP[$order->status],
                'userInfo' => $userInfo,
                'goodsList' => $goodsList,
                'payTime' => $order->pay_time,
                'deduction_balance' => $order->deduction_balance,
                'paymentAmount' => $order->payment_amount,
                'deliveryMode' => $order->delivery_mode,
                'consignee' => $order->consignee,
                'mobile' => $order->mobile,
                'address' => $order->address,
                'orderSn' => $order->order_sn,
                'createdAt' => $order->created_at,
            ];
        });
    }

    public function detail()
    {
        $shopId = $this->verifyRequiredInteger('shopId');
        $orderId = $this->verifyRequiredId('orderId');
        $columns = [
            'id',
            'order_sn',
            'user_id',
            'status',
            'delivery_mode',
            'consignee',
            'mobile',
            'address',
            'pickup_address_id',
            'pickup_time',
            'pickup_mobile',
            'goods_price',
            'freight_price',
            'deduction_balance',
            'payment_amount',
            'refund_amount',
            'pay_time',
            'ship_time',
            'confirm_time',
            'refund_time',
            'remarks',
            'created_at',
            'updated_at',
        ];

        $order = OrderService::getInstance()->getShopOrder($shopId, $orderId, $columns);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }

        $userInfo = UserService::getInstance()->getUserById($order->user_id);
        $order['userInfo'] = $userInfo;
        unset($order->user_id);

        $goodsList = OrderGoodsService::getInstance()->getListByOrderId($order->id);
        $order['goods_list'] = $goodsList;

        $packageList = OrderPackageService::getInstance()->getListByOrderId($order->id);
        $order['package_list'] = $packageList ?: [];

        if ($order->delivery_mode == 2) {
            $pickupAddress = ShopPickupAddressService::getInstance()
                ->getAddressById($order->pickup_address_id, ['id', 'name', 'address_detail', 'latitude', 'longitude']);
            $order['pickup_address'] = $pickupAddress;
            unset($order['pickup_address_id']);
        }

        return $this->success($order);
    }

    public function verify()
    {
        $code = $this->verifyRequiredString('code');

        $verifyCodeInfo = OrderVerifyService::getInstance()->getByCode($code);
        if (is_null($verifyCodeInfo)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '无效核销码');
        }

        $order = OrderService::getInstance()->getPendingVerifyOrderById($verifyCodeInfo->order_id);
        if (is_null($order)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '订单不存在');
        }

        $managerIds = ShopManagerService::getInstance()->getManagerList($order->shop_id)->pluck('user_id')->toArray();
        if ($order->shop_id != $this->user()->shop->id && !in_array($this->userId(), $managerIds)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '非当前商家核销员，无法核销');
        }

        DB::transaction(function () use ($verifyCodeInfo, $order) {
            OrderVerifyService::getInstance()->verify($verifyCodeInfo, $this->userId(), $order->shop_id);
            OrderService::getInstance()->userConfirm($order->user_id, $order->id);
        });

        return $this->success();
    }

    public function unshippedGoodsList()
    {
        $shopId = $this->verifyRequiredInteger('shopId');
        $orderId = $this->verifyRequiredId('orderId');

        $order = OrderService::getInstance()->getShopOrder($shopId, $orderId);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }

        $goodsList = OrderGoodsService::getInstance()->getListByOrderId($order->id)->toArray();
        $packageGoodsList = OrderPackageGoodsService::getInstance()->getListByOrderId($order->id)->toArray();

        $packagedCountMap = [];
        foreach ($packageGoodsList as $packageGoods) {
            $goodsId = $packageGoods['goods_id'];
            $packagedCountMap[$goodsId] = ($packagedCountMap[$goodsId] ?? 0) + $packageGoods['number'];
        }

        $unshippedGoodsList = [];
        foreach ($goodsList as $goods) {
            $goodsId = $goods['goods_id'];
            $totalNumber = $goods['number'];
            $packagedNumber = $packagedCountMap[$goodsId] ?? 0;
            $unshippedNumber = $totalNumber - $packagedNumber;

            if ($unshippedNumber > 0) {
                $goods['number'] = $unshippedNumber;
                $unshippedGoodsList[] = $goods;
            }
        }

        return $this->success($unshippedGoodsList);
    }

    public function ship()
    {
        $shopId = $this->verifyRequiredInteger('shopId');
        $orderId = $this->verifyRequiredInteger('orderId');
        $isAllDelivered = $this->verifyRequiredInteger('isAllDelivered');
        $packageList = $this->verifyArrayNotEmpty('packageList');

        $order = OrderService::getInstance()->getShopOrder($shopId, $orderId);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }
        if (!$order->canShipHandle()) {
            return $this->fail(CodeResponse::ORDER_INVALID_OPERATION, '订单未付款，无法发货');
        }

        OrderService::getInstance()->splitShip($order, $packageList, $isAllDelivered == 1);

        // todo: 管理员操组记录

        return $this->success();
    }
}
