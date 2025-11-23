<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\BusinessException;
use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Imports\OrdersImport;
use App\Models\Order;
use App\Services\ExpressService;
use App\Services\GoodsService;
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
use App\Utils\ExpressServe;
use App\Utils\Inputs\ShopOrderPageInput;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

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
        /** @var ShopOrderPageInput $input */
        $input = ShopOrderPageInput::new();
        $shopId = $this->verifyId('shopId');

        $statusList = $this->statusList($input->status ?? 0);
        $page = OrderService::getInstance()->getShopOrderPage($shopId, $statusList, $input);
        $orderList = collect($page->items());
        $list = $this->handleOrderList($orderList);

        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        $shopId = $this->verifyId('shopId');
        $keywords = $this->verifyRequiredString('keywords');

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
        $orderList = OrderService::getInstance()->searchShopOrderList($shopId, $statusList, $keywords);
        $list = $this->handleOrderList($orderList);

        return $this->success($list);
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
                'updatedAt' => $order->updated_at,
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
            'coupon_denomination',
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

        $userInfo = UserService::getInstance()->getUserById($order->user_id, ['id', 'avatar', 'nickname']);
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

            $verifyInfo = OrderVerifyService::getInstance()->getByOrderId($order->id);
            $order['verify_code'] = $verifyInfo->code ?: null;
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

    public function export()
    {
        $ids = $this->verifyArrayNotEmpty('ids', []);

        OrderService::getInstance()->exportOrderList($ids);

        $excelFile = Excel::raw(new OrdersExport($ids), \Maatwebsite\Excel\Excel::XLSX);
        return response($excelFile)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="orders.xlsx"')
            ->header('X-File-Name', 'orders.xlsx')
            ->header('Access-Control-Expose-Headers', 'X-File-Name');
    }

    public function import()
    {
        $excel = $this->verifyExcel();

        try {
            Excel::import(new OrdersImport(), $excel);
        } catch (\Exception $e) {
            throw new BusinessException(CodeResponse::INVALID_OPERATION, '订单导入失败' . $e->getMessage());
        }

        return $this->success();
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

    public function modifyShipment()
    {
        $id = $this->verifyRequiredInteger('id');
        $packageList = $this->verifyArrayNotEmpty('packageList');

        DB::transaction(function () use ($id, $packageList) {
            OrderPackageService::getInstance()->deleteListByOrderId($id);
            OrderPackageGoodsService::getInstance()->deleteListByOrderId($id);

            foreach ($packageList as $package) {
                $shipChannel = $package['shipChannel'];
                $shipCode = $package['shipCode'];
                $shipSn = $package['shipSn'];
                if (empty($shipCode)) {
                    $express = ExpressService::getInstance()->getExpressByName($shipChannel);
                    $shipCode = $express->code;
                }
                $orderPackage = OrderPackageService::getInstance()->create($id, $shipChannel, $shipCode, $shipSn);

                $goodsList = json_decode($package['goodsList']);
                foreach ($goodsList as $goods) {
                    OrderPackageGoodsService::getInstance()
                        ->create($id, $orderPackage->id, $goods->goodsId, $goods->cover, $goods->name, $goods->selectedSkuName, $goods->number);
                }
            }
        });

        // todo: 管理员操组记录

        return $this->success();
    }

    public function trackingInfo()
    {
        $id = $this->verifyRequiredId('id');
        $package = OrderPackageService::getInstance()->getPackageById($id);

        $order = OrderService::getInstance()->getOrder($package->order_id);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }
        $traces = ExpressServe::new()->track($package->ship_code, $package->ship_sn, $order->mobile);

        return $this->success([
            'shipChannel' => $package->ship_channel,
            'shipSn' => $package->ship_sn,
            'traces' => $traces
        ]);
    }

    public function refund()
    {
        $ids = $this->verifyArrayNotEmpty('ids');
        OrderService::getInstance()->adminRefund($ids);

        // todo: 管理员操组记录

        return $this->success();
    }

    public function confirm()
    {
        $ids = $this->verifyArrayNotEmpty('ids');
        OrderService::getInstance()->adminConfirm($ids);

        // todo: 管理员操组记录

        return $this->success();
    }

    public function shipOrderCount()
    {
        $shopId = $this->verifyRequiredInteger('shopId');
        $count = OrderService::getInstance()->getShopOrderCountByStatusList($shopId, [201, 204]);
        return $this->success($count);
    }

    public function orderedGoodsOptions()
    {
        $shopId = $this->verifyRequiredInteger('shopId');
        $goodsIds = array_unique(OrderGoodsService::getInstance()->getShopList($shopId)->pluck('goods_id')->toArray());
        $goodsOptions = GoodsService::getInstance()->getListByIds($goodsIds, ['id', 'cover', 'name']);
        return $this->success($goodsOptions);
    }

    public function orderedUserOptions()
    {
        $shopId = $this->verifyRequiredInteger('shopId');
        $userIds = OrderService::getInstance()
            ->getShopOrderList($shopId, [201, 202, 301, 302, 401, 402, 403, 501, 502])
            ->pluck('user_id')
            ->toArray();
        $userOptions = UserService::getInstance()->getListByIds($userIds, ['id', 'avatar', 'nickname']);
        return $this->success($userOptions);
    }
}
