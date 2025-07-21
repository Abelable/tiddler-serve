<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Catering\MealTicketOrder;
use App\Models\Catering\OrderMealTicket;
use App\Services\AccountService;
use App\Services\CommissionService;
use App\Services\Mall\Catering\CateringShopIncomeService;
use App\Services\Mall\Catering\CateringShopService;
use App\Services\MealTicketOrderService;
use App\Services\MealTicketService;
use App\Services\MealTicketVerifyService;
use App\Services\OrderMealTicketService;
use App\Services\PromoterService;
use App\Services\RelationService;
use App\Services\RestaurantService;
use App\Utils\CodeResponse;
use App\Utils\Enums\AccountChangeType;
use App\Utils\Enums\MealTicketOrderStatus;
use App\Utils\Enums\ProductType;
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

        // 判断余额状态
        if (!is_null($input->useBalance) && $input->useBalance != 0) {
            $account = AccountService::getInstance()->getUserAccount($this->userId());
            if ($account->status == 0 || $account->balance <= 0) {
                return $this->fail(CodeResponse::NOT_FOUND, '余额异常不可用，请联系客服解决问题');
            }
        }

        $promoterInfo = $this->user()->promoterInfo;
        $userId = $this->userId();
        $userLevel = $promoterInfo ? $promoterInfo->level : 0;
        $superiorId = RelationService::getInstance()->getSuperiorId($userId);
        $superiorLevel = PromoterService::getInstance()->getPromoterLevel($superiorId);
        $upperSuperiorId = RelationService::getInstance()->getSuperiorId($superiorId);
        $upperSuperiorLevel = PromoterService::getInstance()->getPromoterLevel($upperSuperiorId);

        $ticket = MealTicketService::getInstance()->getTicketById($input->ticketId);
        $shop = CateringShopService::getInstance()->getShopById($ticket->shop_id);

        $totalPrice = (float)bcmul($ticket->price, $input->num, 2);
        $paymentAmount = $totalPrice;

        // 余额抵扣
        $deductionBalance = 0;
        if ($input->useBalance == 1) {
            $account = AccountService::getInstance()->getUserAccount($userId);
            $deductionBalance = min($paymentAmount, $account->balance);
            $paymentAmount = bcsub($paymentAmount, $deductionBalance, 2);
        }

        $orderId = DB::transaction(function () use (
            $totalPrice,
            $shop,
            $deductionBalance,
            $upperSuperiorLevel,
            $upperSuperiorId,
            $superiorLevel,
            $superiorId,
            $userLevel,
            $userId,
            $paymentAmount,
            $ticket,
            $input
        ) {
            $order = MealTicketOrderService::getInstance()->createOrder(
                $this->user(),
                $shop,
                $totalPrice,
                $deductionBalance,
                $paymentAmount
            );

            // 生成订单代金券快照
            OrderMealTicketService::getInstance()->createOrderTicket(
                $userId,
                $order->id,
                $input->restaurantId,
                $input->restaurantName,
                $input->num,
                $ticket
            );

            // 生成核销码
            MealTicketVerifyService::getInstance()->createVerifyCode($order->id, $input->restaurantId);

            // 生成佣金记录
            CommissionService::getInstance()->createMealTicketCommission(
                $order->id,
                $order->order_sn,
                $ticket,
                $paymentAmount,
                $userId,
                $userLevel,
                $superiorId,
                $superiorLevel,
                $upperSuperiorId,
                $upperSuperiorLevel
            );

            // 生成店铺收益
            CateringShopIncomeService::getInstance()->createIncome(
                $shop->id,
                $order->id,
                $order->order_sn,
                ProductType::MEAL_TICKET,
                $ticket->id,
                $ticket->sales_commission_rate,
                $paymentAmount
            );

            if ($input->useBalance == 1) {
                // 更新余额
                AccountService::getInstance()->updateBalance(
                    $userId,
                    AccountChangeType::PURCHASE,
                    -$deductionBalance,
                    $order->order_sn,
                    ProductType::MEAL_TICKET
                );
            }

            // 增加餐馆、代金券销量
            RestaurantService::getInstance()->increaseSalesVolume($input->restaurantId, $input->num);
            $ticket->sales_volume = $ticket->sales_volume + $input->num;
            $ticket->save();

            return $order->id;
        });

        return $this->success($orderId);
    }

    public function payParams()
    {
        $orderId = $this->verifyRequiredInteger('orderId');
        $order = MealTicketOrderService::getInstance()
            ->createWxPayOrder($this->userId(), $orderId, $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($order);
        return $this->success($payParams);
    }

    public function total()
    {
        return $this->success([
            MealTicketOrderService::getInstance()->getTotal($this->userId(), $this->statusList(1)),
            MealTicketOrderService::getInstance()->getTotal($this->userId(), $this->statusList(2)),
            MealTicketOrderService::getInstance()->getTotal($this->userId(), $this->statusList(3)),
            MealTicketOrderService::getInstance()->getTotal($this->userId(), $this->statusList(4)),
            MealTicketOrderService::getInstance()->getTotal($this->userId(), [MealTicketOrderStatus::REFUNDING]),
        ]);
    }

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');

        $statusList = $this->statusList($status);
        $page = MealTicketOrderService::getInstance()->getOrderListByStatus($this->userId(), $statusList, $input);
        $list = $this->handleOrderList(collect($page->items()));

        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        $keywords = $this->verifyRequiredString('keywords');

        $orderGoodsList = OrderMealTicketService::getInstance()->searchList($this->userId(), $keywords);
        $orderIds = $orderGoodsList->pluck('order_id')->toArray();
        $orderList = MealTicketOrderService::getInstance()->getOrderListByIds($orderIds);
        $list = $this->handleOrderList($orderList);

        return $this->success($list);
    }

    private function statusList($status) {
        switch ($status) {
            case 1:
                $statusList = [MealTicketOrderStatus::CREATED];
                break;
            case 2:
                $statusList = [MealTicketOrderStatus::PAID];
                break;
            case 3:
                $statusList = [MealTicketOrderStatus::MERCHANT_APPROVED];
                break;
            case 4:
                $statusList = [
                    MealTicketOrderStatus::CONFIRMED,
                    MealTicketOrderStatus::AUTO_CONFIRMED,
                    MealTicketOrderStatus::ADMIN_CONFIRMED
                ];
                break;
            case 5:
                $statusList = [
                    MealTicketOrderStatus::REFUNDING,
                    MealTicketOrderStatus::REFUNDED,
                    MealTicketOrderStatus::MERCHANT_REJECTED
                ];
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
        $ticketList = OrderMealTicketService::getInstance()->getListByOrderIds($orderIds)->keyBy('order_id');
        return $orderList->map(function (MealTicketOrder $order) use ($ticketList) {
            /** @var OrderMealTicket $ticket */
            $ticket = $ticketList->get($order->id);
            $ticket->use_time_list = json_decode($ticket->use_time_list) ?: [];
            $ticket->inapplicable_products = json_decode($ticket->inapplicable_products) ?: [];
            $ticket->use_rules = json_decode($ticket->use_rules) ?: [];

            return [
                'id' => $order->id,
                'orderSn' => $order->order_sn,
                'status' => $order->status,
                'statusDesc' => MealTicketOrderStatus::TEXT_MAP[$order->status],
                'shopId' => $order->shop_id,
                'shopLogo' => $order->shop_logo,
                'shopName' => $order->shop_name,
                'consignee' => $order->consignee,
                'mobile' => $order->mobile,
                'ticketInfo' => $ticket,
                'totalPrice' => $order->total_price,
                'paymentAmount' => $order->payment_amount,
                'payTime' => $order->pay_time,
                'createdAt' => $order->created_at
            ];
        });
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $columns = [
            'id',
            'order_sn',
            'status',
            'shop_id',
            'shop_logo',
            'shop_name',
            'consignee',
            'mobile',
            'total_price',
            'payment_amount',
            'pay_time',
            'approve_time',
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

    public function verifyCode()
    {
        $orderId = $this->verifyRequiredId('orderId');
        $restaurantId = $this->verifyRequiredId('restaurantId');

        $verifyCodeInfo = MealTicketVerifyService::getInstance()
            ->getVerifyCodeInfo($orderId, $restaurantId);
        if (is_null($verifyCodeInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '核销信息不存在');
        }

        return $this->success($verifyCodeInfo->code);
    }

    public function cancel()
    {
        $id = $this->verifyRequiredId('id');
        MealTicketOrderService::getInstance()->userCancel($this->userId(), $id);
        return $this->success();
    }

    public function refund()
    {
        $id = $this->verifyRequiredId('id');
        MealTicketOrderService::getInstance()->userRefund($this->userId(), $id);
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        DB::transaction(function () use ($id) {
            MealTicketOrderService::getInstance()->delete($this->userId(), $id);
            OrderMealTicketService::getInstance()->delete($id);
        });
        return $this->success();
    }
}
