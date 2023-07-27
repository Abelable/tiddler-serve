<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderGoodsService;
use App\Services\OrderService;
use App\Services\ScenicOrderService;
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

        list($paymentAmount) = ScenicOrderService::getInstance()->calcPaymentAmount($ticketId, $categoryId, $timeStamp, $num);

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

        $orderId = DB::transaction(function () use ($input) {
            return ScenicOrderService::getInstance()->createOrder($this->userId(), $input);
        });

        return $this->success($orderId);
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
