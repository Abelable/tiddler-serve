<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Catering\MealTicketOrder;
use App\Models\Catering\OrderMealTicket;
use App\Services\AccountService;
use App\Services\CommissionService;
use App\Services\MealTicketOrderService;
use App\Services\MealTicketService;
use App\Services\MealTicketVerifyService;
use App\Services\OrderMealTicketService;
use App\Services\PromoterService;
use App\Services\RelationService;
use App\Services\RestaurantManagerService;
use App\Services\RestaurantService;
use App\Utils\CodeResponse;
use App\Utils\Enums\MealTicketOrderStatus;
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

        $userId = $this->userId();
        $userLevel = $this->user()->promoterInfo->level ?: 0;
        $superiorId = RelationService::getInstance()->getSuperiorId($userId);
        $superiorLevel = PromoterService::getInstance()->getPromoterLevel($superiorId);
        $upperSuperiorId = RelationService::getInstance()->getSuperiorId($superiorId);
        $upperSuperiorLevel = PromoterService::getInstance()->getPromoterLevel($upperSuperiorId);

        $ticket = MealTicketService::getInstance()->getTicketById($input->ticketId);
        $paymentAmount = (float)bcmul($ticket->price, $input->num, 2);

        $orderId = DB::transaction(function () use (
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
            $order = MealTicketOrderService::getInstance()->createOrder($this->user(), $input, $ticket->provider_id, $paymentAmount);

            // 生成核销码
            MealTicketVerifyService::getInstance()->createVerifyCode($order->id, $input->restaurantId);

            // 生成订单代金券快照
            OrderMealTicketService::getInstance()->createOrderTicket($order->id, $input->num, $ticket);

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
        $page = MealTicketOrderService::getInstance()
            ->getProviderOrderList($this->user()->cateringProvider->id, $statusList, $input);
        $list = $this->orderList($page);

        return $this->success($this->paginate($page, $list));
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
                $statusList = [MealTicketOrderStatus::CONFIRMED, MealTicketOrderStatus::AUTO_CONFIRMED];
                break;
            case 4:
                $statusList = [MealTicketOrderStatus::REFUNDING, MealTicketOrderStatus::REFUNDED];
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
                'statusDesc' => MealTicketOrderStatus::TEXT_MAP[$order->status],
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

    public function verifyCode()
    {
        $orderId = $this->verifyRequiredId('orderId');
        $restaurantId = $this->verifyRequiredId('hotelId');

        $verifyCodeInfo = MealTicketVerifyService::getInstance()->getVerifyCodeInfo($orderId, $restaurantId);
        if (is_null($verifyCodeInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '核销信息不存在');
        }

        return $this->success($verifyCodeInfo->code);
    }

    public function verify()
    {
        $code = $this->verifyRequiredString('code');

        $verifyCodeInfo = MealTicketVerifyService::getInstance()->getByCode($code);
        if (is_null($verifyCodeInfo)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '无效核销码');
        }

        $order = MealTicketOrderService::getInstance()->getPaidOrderById($verifyCodeInfo->order_id);
        if (is_null($order)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '订单不存在');
        }

        $managerIds = RestaurantManagerService::getInstance()
            ->getManagerList($verifyCodeInfo->restaurant_id)->pluck('user_id')->toArray();
        if (!in_array($this->userId(), $managerIds)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '非当前餐馆核销员，无法核销');
        }

        DB::transaction(function () use ($verifyCodeInfo, $order) {
            MealTicketVerifyService::getInstance()->verify($verifyCodeInfo, $this->userId());
            MealTicketOrderService::getInstance()->userConfirm($order->user_id, $order->id);
        });

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
        MealTicketOrderService::getInstance()->userRefund($this->userId(), $id);
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
