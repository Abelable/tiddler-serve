<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ScenicOrder;
use App\Services\ScenicOrderService;
use App\Services\ScenicOrderTicketService;
use App\Utils\CodeResponse;
use App\Utils\Enums\ScenicOrderEnums;
use App\Utils\Inputs\ScenicOrderInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yansongda\LaravelPay\Facades\Pay;

class MealTicketOrderController extends Controller
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
        /** @var ScenicOrderInput $input */
        $input = ScenicOrderInput::new();

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
        $orderId = $this->verifyRequiredInteger('orderId');
        $order = ScenicOrderService::getInstance()->createWxPayOrder($this->userId(), $orderId, $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($order);
        return $this->success($payParams);
    }

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');

        $statusList = $this->statusList($status);
        $page = ScenicOrderService::getInstance()->getOrderListByStatus($this->userId(), $statusList, $input);
        $list = $this->orderList($page);

        return $this->success($this->paginate($page, $list));
    }

    public function shopList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');
        $shopId = $this->verifyId('shopId');

        $statusList = $this->statusList($status);
        $page = ScenicOrderService::getInstance()->getShopOrderList($shopId, $statusList, $input);
        $list = $this->orderList($page);

        return $this->success($this->paginate($page, $list));
    }

    private function statusList($status) {
        switch ($status) {
            case 1:
                $statusList = [ScenicOrderEnums::STATUS_CREATE];
                break;
            case 2:
                $statusList = [ScenicOrderEnums::STATUS_PAY];
                break;
            case 3:
                $statusList = [ScenicOrderEnums::STATUS_CONFIRM, ScenicOrderEnums::STATUS_AUTO_CONFIRM];
                break;
            case 4:
                $statusList = [ScenicOrderEnums::STATUS_REFUND, ScenicOrderEnums::STATUS_REFUND_CONFIRM];
                break;
            default:
                $statusList = [];
                break;
        }

        return $statusList;
    }

    private function orderList($page)
    {
        $orderList = collect($page->items());
        $orderIds = $orderList->pluck('id')->toArray();
        $ticketList = ScenicOrderTicketService::getInstance()->getListByOrderIds($orderIds)->keyBy('order_id');
        return $orderList->map(function (ScenicOrder $order) use ($ticketList) {
            $ticket = $ticketList->get($order->id);
            return [
                'id' => $order->id,
                'status' => $order->status,
                'statusDesc' => ScenicOrderEnums::STATUS_TEXT_MAP[$order->status],
                'shopId' => $order->shop_id,
                'shopAvatar' => $order->shop_avatar,
                'shopName' => $order->shop_name,
                'ticketInfo' => $ticket,
                'paymentAmount' => $order->payment_amount,
                'consignee' => $order->consignee,
                'mobile' => $order->mobile,
                'orderSn' => $order->order_sn
            ];
        });
    }

    public function cancel()
    {
        $id = $this->verifyRequiredId('id');
        ScenicOrderService::getInstance()->userCancel($this->userId(), $id);
        return $this->success();
    }

    public function confirm()
    {
        $id = $this->verifyRequiredId('id');
        ScenicOrderService::getInstance()->confirm($this->userId(), $id);
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        DB::transaction(function () use ($id) {
            ScenicOrderService::getInstance()->delete($this->userId(), $id);
        });
        return $this->success();
    }

    public function refund()
    {
        $id = $this->verifyRequiredId('id');
        ScenicOrderService::getInstance()->refund($this->userId(), $id);
        return $this->success();
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $columns = [
            'id',
            'order_sn',
            'status',
            'consignee',
            'mobile',
            'shop_id',
            'shop_avatar',
            'shop_name',
            'payment_amount',
            'pay_time',
            'confirm_time',
            'created_at',
            'updated_at',
        ];
        $order = ScenicOrderService::getInstance()->getOrderById($this->userId(), $id, $columns);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }
        $ticket = ScenicOrderTicketService::getInstance()->getTicketByOrderId($order->id);
        $order['ticketInfo'] = $ticket;
        return $this->success($order);
    }
}
