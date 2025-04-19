<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\MealTicketOrder;
use App\Models\OrderMealTicket;
use App\Services\AccountService;
use App\Services\MealTicketOrderService;
use App\Services\MealTicketService;
use App\Services\OrderMealTicketService;
use App\Utils\CodeResponse;
use App\Utils\Enums\MealTicketOrderEnums;
use App\Utils\Inputs\MealTicketOrderInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yansongda\LaravelPay\Facades\Pay;

class MealTicketOrderController extends Controller
{
    public function paymentAmount()
    {
        $ticketId = $this->verifyRequiredId('ticketId');
        $num = $this->verifyRequiredInteger('num');
        $useBalance = $this->verifyBoolean('useBalance', false);

        $ticket = MealTicketService::getInstance()->getTicketById($ticketId);
        $totalPrice = (float)bcmul($ticket->price, $num, 2);

        // 余额逻辑
        $deductionBalance = 0;
        $account = AccountService::getInstance()->getUserAccount($this->userId());
        $accountBalance = $account->status == 1 ? $account->balance : 0;
        if ($useBalance) {
            $deductionBalance = min($totalPrice, $accountBalance);
            $paymentAmount = bcsub($totalPrice, $deductionBalance, 2);
        } else {
            $paymentAmount = $totalPrice;
        }

        return $this->success([
            'totalPrice' => $totalPrice,
            'accountBalance' => $accountBalance,
            'deductionBalance' => $deductionBalance,
            'paymentAmount' => $paymentAmount
        ]);
    }

    public function submit()
    {
        /** @var MealTicketOrderInput $input */
        $input = MealTicketOrderInput::new();

        // 分布式锁，防止重复请求
        $lockKey = sprintf('create_meal_ticket_order_%s_%s', $this->userId(), md5(serialize($input)));
        $lock = Cache::lock($lockKey, 5);
        if (!$lock->get()) {
            $this->fail(CodeResponse::FAIL, '请勿重复提交订单');
        }

        $orderId = DB::transaction(function () use ($input) {
            return MealTicketOrderService::getInstance()->createOrder($this->user(), $input);
        });

        return $this->success($orderId);
    }

    public function payParams()
    {
        $orderId = $this->verifyRequiredInteger('orderId');
        $order = MealTicketOrderService::getInstance()->createWxPayOrder($this->userId(), $orderId, $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($order);
        return $this->success($payParams);
    }

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');

        $statusList = $this->statusList($status);
        $page = MealTicketOrderService::getInstance()->getOrderListByStatus($this->userId(), $statusList, $input);
        $list = $this->orderList($page);

        return $this->success($this->paginate($page, $list));
    }

    public function providerList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');

        $statusList = $this->statusList($status);
        $page = MealTicketOrderService::getInstance()->getProviderOrderList($this->user()->cateringProvider->id, $statusList, $input);
        $list = $this->orderList($page);

        return $this->success($this->paginate($page, $list));
    }

    private function statusList($status) {
        switch ($status) {
            case 1:
                $statusList = [MealTicketOrderEnums::STATUS_CREATE];
                break;
            case 2:
                $statusList = [MealTicketOrderEnums::STATUS_PAY];
                break;
            case 3:
                $statusList = [MealTicketOrderEnums::STATUS_CONFIRM, MealTicketOrderEnums::STATUS_AUTO_CONFIRM];
                break;
            case 4:
                $statusList = [MealTicketOrderEnums::STATUS_REFUND, MealTicketOrderEnums::STATUS_REFUND_CONFIRM];
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
        $ticketList = OrderMealTicketService::getInstance()->getListByOrderIds($orderIds)->keyBy('order_id');
        return $orderList->map(function (MealTicketOrder $order) use ($ticketList) {
            /** @var OrderMealTicket $ticket */
            $ticket = $ticketList->get($order->id);
            $ticket->use_time_list = json_decode($ticket->use_time_list) ?: [];
            $ticket->inapplicable_products = json_decode($ticket->inapplicable_products) ?: [];
            $ticket->use_rules = json_decode($ticket->use_rules) ?: [];

            return [
                'id' => $order->id,
                'status' => $order->status,
                'statusDesc' => MealTicketOrderEnums::STATUS_TEXT_MAP[$order->status],
                'restaurantId' => $order->restaurant_id,
                'restaurantName' => $order->restaurant_name,
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
        MealTicketOrderService::getInstance()->userCancel($this->userId(), $id);
        return $this->success();
    }

    public function confirm()
    {
        $id = $this->verifyRequiredId('id');
        MealTicketOrderService::getInstance()->confirm($this->userId(), $id);
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        DB::transaction(function () use ($id) {
            MealTicketOrderService::getInstance()->delete($this->userId(), $id);
        });
        return $this->success();
    }

    public function refund()
    {
        $id = $this->verifyRequiredId('id');
        MealTicketOrderService::getInstance()->refund($this->userId(), $id);
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
            'restaurant_id',
            'restaurant_name',
            'payment_amount',
            'pay_time',
            'confirm_time',
            'created_at',
            'updated_at',
        ];
        $order = MealTicketOrderService::getInstance()->getOrderById($this->userId(), $id, $columns);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }

        $ticket = OrderMealTicketService::getInstance()->getTicketByOrderId($order->id);
        $ticket->use_time_list = json_decode($ticket->use_time_list) ?: [];
        $ticket->inapplicable_products = json_decode($ticket->inapplicable_products) ?: [];
        $ticket->use_rules = json_decode($ticket->use_rules) ?: [];
        $order['ticketInfo'] = $ticket;

        return $this->success($order);
    }
}
