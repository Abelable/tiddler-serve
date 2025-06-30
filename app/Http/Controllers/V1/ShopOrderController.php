<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderGoodsService;
use App\Services\OrderPackageService;
use App\Services\OrderService;
use App\Services\OrderVerifyService;
use App\Services\ShopManagerService;
use App\Services\ShopPickupAddressService;
use App\Utils\CodeResponse;
use App\Utils\Enums\OrderEnums;
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
            OrderService::getInstance()->getShopTotal($shopId, [OrderEnums::STATUS_REFUND]),
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
                $statusList = [OrderEnums::STATUS_PAY, OrderEnums::STATUS_EXPORTED];
                break;
            case 2:
                $statusList = [OrderEnums::STATUS_SHIP];
                break;
            case 3:
                $statusList = [OrderEnums::STATUS_PENDING_VERIFICATION];
                break;
            case 4:
                $statusList = [OrderEnums::STATUS_FINISHED];
                break;
            case 5:
                $statusList = [OrderEnums::STATUS_REFUND, OrderEnums::STATUS_REFUND_CONFIRM];
                break;
            default:
                $statusList = [];
                break;
        }

        return $statusList;
    }

    private function handleOrderList($orderList)
    {
        $orderIds = $orderList->pluck('id')->toArray();
        $goodsListColumns = ['order_id', 'goods_id', 'cover', 'name', 'selected_sku_name', 'price', 'number'];
        $groupedGoodsList = OrderGoodsService::getInstance()->getListByOrderIds($orderIds, $goodsListColumns)->groupBy('order_id');
        return $orderList->map(function (Order $order) use ($groupedGoodsList) {
            $goodsList = $groupedGoodsList->get($order->id);
            return [
                'id' => $order->id,
                'status' => $order->status,
                'statusDesc' => OrderEnums::STATUS_TEXT_MAP[$order->status],
                'goodsList' => $goodsList,
                'payTime' => $order->pay_time,
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

        $goodsList = OrderGoodsService::getInstance()->getListByOrderId($order->id);
        $order['goods_list'] = $goodsList;

        $packageList = OrderPackageService::getInstance()->getListByOrderId($order->id);
        $order['package_list'] = $packageList ?: [];

        if ($order->delivery_mode == 2) {
            $pickupAddress = ShopPickupAddressService::getInstance()
                ->getAddressById($order->pickup_address_id, ['id', 'name', 'address_detail', 'latitude', 'longitude']);
            $order['pickup_address'] = $pickupAddress;
            unset($order['pickup_address_id']);

            if ($order->status !== OrderEnums::STATUS_CREATE) {
                $verifyInfo = OrderVerifyService::getInstance()->getByOrderId($order->id);
                $order['verify_code'] = $verifyInfo->code ?: null;
            }
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
        if (!in_array($this->userId(), $managerIds)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '非当前商家核销员，无法核销');
        }

        DB::transaction(function () use ($verifyCodeInfo, $order) {
            OrderVerifyService::getInstance()->verify($verifyCodeInfo, $this->userId(), $order->shop_id);
            OrderService::getInstance()->userConfirm($order->user_id, $order->id);
        });

        return $this->success();
    }

    public function delivery()
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
