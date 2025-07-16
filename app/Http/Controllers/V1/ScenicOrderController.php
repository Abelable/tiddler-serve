<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ScenicOrder;
use App\Models\ScenicOrderTicket;
use App\Models\ScenicSpot;
use App\Services\AccountService;
use App\Services\CommissionService;
use App\Services\PromoterService;
use App\Services\RelationService;
use App\Services\ScenicOrderService;
use App\Services\ScenicOrderTicketService;
use App\Services\ScenicOrderVerifyService;
use App\Services\ScenicService;
use App\Services\ScenicShopIncomeService;
use App\Services\ScenicShopService;
use App\Services\ScenicTicketCategoryService;
use App\Services\ScenicTicketService;
use App\Services\TicketScenicService;
use App\Services\TicketSpecService;
use App\Utils\CodeResponse;
use App\Utils\Enums\AccountChangeType;
use App\Utils\Enums\ProductType;
use App\Utils\Enums\ScenicOrderStatus;
use App\Utils\Inputs\ScenicOrderInput;
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
        $useBalance = $this->verifyBoolean('useBalance', false);

        $priceUnit = TicketSpecService::getInstance()->getPriceUnit($ticketId, $categoryId, $timeStamp);
        $totalPrice = (float)bcmul($priceUnit->price, $num, 2);

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
        /** @var ScenicOrderInput $input */
        $input = ScenicOrderInput::new();

        // 分布式锁，防止重复请求
        $lockKey = sprintf('create_scenic_order_%s_%s', $this->userId(), md5(serialize($input)));
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

        $ticket = ScenicTicketService::getInstance()->getTicketById($input->ticketId);

        $ticketScenicIds = TicketScenicService::getInstance()->getListByTicketId($ticket->id)->pluck('scenic_id')->toArray();
        $scenicList = ScenicService::getInstance()
            ->getScenicListByIds($ticketScenicIds, ['id', 'name', 'image_list'])
            ->map(function (ScenicSpot $scenic) {
                $scenic['cover'] = json_decode($scenic->image_list, true)[0];
                unset($scenic['image_list']);
                return $scenic;
            })
            ->toArray();

        $shop = ScenicShopService::getInstance()->getShopById($ticket->shop_id);

        $priceUnit = TicketSpecService::getInstance()->getPriceUnit($input->ticketId, $input->categoryId, $input->timeStamp);
        $totalPrice = (float)bcmul($priceUnit->price, $input->num, 2);
        $paymentAmount = $totalPrice;

        // 余额抵扣
        $deductionBalance = 0;
        if ($input->useBalance == 1) {
            $account = AccountService::getInstance()->getUserAccount($userId);
            $deductionBalance = min($paymentAmount, $account->balance);
            $paymentAmount = bcsub($paymentAmount, $deductionBalance, 2);
        }

        $orderId = DB::transaction(function () use ($scenicList, $totalPrice, $deductionBalance, $ticketScenicIds, $upperSuperiorLevel, $upperSuperiorId, $superiorLevel, $superiorId, $userLevel, $userId, $paymentAmount, $shop, $ticket, $priceUnit, $input) {
            // 生成订单
            $order = ScenicOrderService::getInstance()
                ->createOrder($userId, $input, $shop, $totalPrice, $deductionBalance, $paymentAmount);

            // 生成订单门票快照
            $category = ScenicTicketCategoryService::getInstance()->getCategoryById($input->categoryId);
            ScenicOrderTicketService::getInstance()
                ->createOrderTicket($userId, $order->id, $category, $input->timeStamp, $priceUnit, $input->num, $ticket, $scenicList);

            // 生成景点对应核销码
            foreach ($ticketScenicIds as $scenicId) {
                ScenicOrderVerifyService::getInstance()->createVerifyCode($order->id, $scenicId);
            }

            // 生成佣金记录
            CommissionService::getInstance()->createScenicCommission(
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
            ScenicShopIncomeService::getInstance()
                ->createIncome($shop->id, $order->id, $order->order_sn, $ticket, $paymentAmount);

            // 更新余额
            if ($input->useBalance == 1) {
                AccountService::getInstance()->updateBalance(
                    $userId,
                    AccountChangeType::PURCHASE,
                    -$deductionBalance,
                    $order->order_sn,
                    ProductType::SCENIC
                );
            }

            // 增加景点、门票销量
            ScenicService::getInstance()->addSalesVolumeByIds($ticketScenicIds, $input->num);
            $ticket->sales_volume = $ticket->sales_volume + $input->num;
            $ticket->save();

            return $order->id;
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

    public function total()
    {
        return $this->success([
            ScenicOrderService::getInstance()->getTotal($this->userId(), $this->statusList(1)),
            ScenicOrderService::getInstance()->getTotal($this->userId(), $this->statusList(2)),
            ScenicOrderService::getInstance()->getTotal($this->userId(), $this->statusList(3)),
            ScenicOrderService::getInstance()->getTotal($this->userId(), $this->statusList(4)),
            ScenicOrderService::getInstance()->getTotal($this->userId(), [ScenicOrderStatus::REFUNDING]),
        ]);
    }

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');

        $statusList = $this->statusList($status);
        $page = ScenicOrderService::getInstance()->getOrderListByStatus($this->userId(), $statusList, $input);
        $orderList = collect($page->items());
        $list = $this->handleOrderList($orderList);

        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        $keywords = $this->verifyRequiredString('keywords');

        $orderGoodsList = ScenicOrderTicketService::getInstance()->searchList($this->userId(), $keywords);
        $orderIds = $orderGoodsList->pluck('order_id')->toArray();
        $orderList = ScenicOrderService::getInstance()->getOrderListByIds($orderIds);
        $list = $this->handleOrderList($orderList);

        return $this->success($list);
    }

    private function statusList($status) {
        switch ($status) {
            case 1:
                $statusList = [ScenicOrderStatus::CREATED];
                break;
            case 2:
                $statusList = [ScenicOrderStatus::PAID];
                break;
            case 3:
                $statusList = [ScenicOrderStatus::MERCHANT_APPROVED];
                break;
            case 4:
                $statusList = [
                    ScenicOrderStatus::CONFIRMED,
                    ScenicOrderStatus::AUTO_CONFIRMED,
                    ScenicOrderStatus::ADMIN_CONFIRMED
                ];
                break;
            case 5:
                $statusList = [
                    ScenicOrderStatus::REFUNDING,
                    ScenicOrderStatus::REFUNDED,
                    ScenicOrderStatus::MERCHANT_REJECTED
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
        $ticketList = ScenicOrderTicketService::getInstance()
            ->getListByOrderIds($orderIds)
            ->keyBy('order_id');

        return $orderList->map(function (ScenicOrder $order) use ($ticketList) {
            /** @var ScenicOrderTicket $ticket */
            $ticket = $ticketList->get($order->id);

            return [
                'id' => $order->id,
                'orderSn' => $order->order_sn,
                'status' => $order->status,
                'statusDesc' => ScenicOrderStatus::TEXT_MAP[$order->status],
                'shopId' => $order->shop_id,
                'shopLogo' => $order->shop_logo,
                'shopName' => $order->shop_name,
                'ticketInfo' => [
                    'id' => $ticket->ticket_id,
                    'name' => $ticket->name,
                    'categoryName' => $ticket->category_name,
                    'price' => $ticket->price,
                    'number' => $ticket->number,
                    'scenicList' => json_decode($ticket->scenic_list),
                    'validityTime' => $ticket->validity_time,
                    'selectedDateTimestamp' => $ticket->selected_date_timestamp,
                    'effectiveTime' => $ticket->effective_time,
                    'refundStatus' => $ticket->refund_status,
                    'needExchange' => $ticket->need_exchange
                ],
                'totalPrice' => $order->total_price,
                'paymentAmount' => $order->payment_amount,
                'consignee' => $order->consignee,
                'mobile' => $order->mobile,
                'idCardNumber' => $order->id_card_number,
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
            'user_id',
            'consignee',
            'mobile',
            'shop_id',
            'shop_logo',
            'shop_name',
            'total_price',
            'payment_amount',
            'pay_time',
            'approve_time',
            'confirm_time',
            'created_at',
            'updated_at',
        ];
        $order = ScenicOrderService::getInstance()->getUserOrder($this->userId(), $id, $columns);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }

        $ticket = ScenicOrderTicketService::getInstance()->getTicketByOrderId($order->id);
        $order['ticketInfo'] =  [
            'id' => $ticket->ticket_id,
            'name' => $ticket->name,
            'categoryName' => $ticket->category_name,
            'price' => $ticket->price,
            'number' => $ticket->number,
            'scenicList' => json_decode($ticket->scenic_list),
            'validityTime' => $ticket->validity_time,
            'selectedDateTimestamp' => $ticket->selected_date_timestamp,
            'effectiveTime' => $ticket->effective_time,
            'refundStatus' => $ticket->refund_status,
            'needExchange' => $ticket->need_exchange
        ];

        return $this->success($order);
    }

    public function cancel()
    {
        $id = $this->verifyRequiredId('id');
        ScenicOrderService::getInstance()->userCancel($this->userId(), $id);
        return $this->success();
    }

    public function verifyCode()
    {
        $orderId = $this->verifyRequiredId('orderId');
        $scenicId = $this->verifyRequiredId('scenicId');

        $verifyCodeInfo = ScenicOrderVerifyService::getInstance()->getVerifyCodeInfo($orderId, $scenicId);
        if (is_null($verifyCodeInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '核销信息不存在');
        }

        return $this->success($verifyCodeInfo->code);
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        DB::transaction(function () use ($id) {
            ScenicOrderService::getInstance()->delete($this->userId(), $id);
            ScenicOrderTicketService::getInstance()->delete($id);
        });

        return $this->success();
    }

    public function refund()
    {
        $id = $this->verifyRequiredId('id');
        ScenicOrderService::getInstance()->userRefund($this->userId(), $id);
        return $this->success();
    }
}
