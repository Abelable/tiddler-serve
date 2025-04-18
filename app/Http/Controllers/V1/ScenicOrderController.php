<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ScenicOrder;
use App\Services\AccountService;
use App\Services\CommissionService;
use App\Services\PromoterService;
use App\Services\RelationService;
use App\Services\ScenicManagerService;
use App\Services\ScenicOrderService;
use App\Services\ScenicOrderTicketService;
use App\Services\ScenicOrderVerifyService;
use App\Services\ScenicShopService;
use App\Services\ScenicTicketCategoryService;
use App\Services\ScenicTicketService;
use App\Services\TicketScenicService;
use App\Services\TicketSpecService;
use App\Utils\CodeResponse;
use App\Utils\Enums\ScenicOrderEnums;
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

        $userId = $this->userId();
        $userLevel = $this->user()->promoterInfo->level ?: 0;
        $superiorId = RelationService::getInstance()->getSuperiorId($userId);
        $superiorLevel = PromoterService::getInstance()->getPromoterLevel($superiorId);
        $upperSuperiorId = RelationService::getInstance()->getSuperiorId($superiorId);
        $upperSuperiorLevel = PromoterService::getInstance()->getPromoterLevel($upperSuperiorId);

        $ticket = ScenicTicketService::getInstance()->getTicketById($input->ticketId);
        $ticketScenicIds = TicketScenicService::getInstance()->getListByTicketId($ticket->id)->pluck('scenic_id')->toArray();
        $shop = ScenicShopService::getInstance()->getShopById($ticket->shop_id);

        $priceUnit = TicketSpecService::getInstance()->getPriceUnit($input->ticketId, $input->categoryId, $input->timeStamp);
        $paymentAmount = (float)bcmul($priceUnit->price, $input->num, 2);

        $orderId = DB::transaction(function () use ($ticketScenicIds, $upperSuperiorLevel, $upperSuperiorId, $superiorLevel, $superiorId, $userLevel, $userId, $paymentAmount, $shop, $ticket, $priceUnit, $input) {
            $order = ScenicOrderService::getInstance()->createOrder($this->userId(), $input, $shop, $paymentAmount);

            // 生成景点对应核销码
            foreach ($ticketScenicIds as $scenicId) {
                ScenicOrderVerifyService::getInstance()->createVerifyCode($order->id, $scenicId);
            }

            // 生成订单门票快照
            $category = ScenicTicketCategoryService::getInstance()->getCategoryById($input->categoryId);
            ScenicOrderTicketService::getInstance()
                ->createOrderTicket($order->id, $category, $input->timeStamp, $priceUnit, $input->num, $ticket);

            // 生成佣金记录
            CommissionService::getInstance()
                ->createScenicCommission($order->id, $ticket, $priceUnit, $paymentAmount, $userId, $userLevel, $superiorId, $superiorLevel, $upperSuperiorId, $upperSuperiorLevel);

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
                'shopLogo' => $order->shop_logo,
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

    public function verify()
    {
        $code = $this->verifyRequiredString('code');

        $verifyCodeInfo = ScenicOrderVerifyService::getInstance()->getByCode($code);
        if (is_null($verifyCodeInfo)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '无效核销码');
        }

        $order = ScenicOrderService::getInstance()->getPaidOrderById($verifyCodeInfo->order_id);
        if (is_null($order)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '订单不存在');
        }

        $managerIds = ScenicManagerService::getInstance()
            ->getManagerList($verifyCodeInfo->scenic_id)->pluck('user_id')->toArray();
        if (!in_array($this->userId(), $managerIds)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '非当前景点核销员，无法核销');
        }

        DB::transaction(function () use ($verifyCodeInfo, $order) {
            ScenicOrderVerifyService::getInstance()->verify($verifyCodeInfo, $this->userId());

            if (!ScenicOrderVerifyService::getInstance()->hasUnverifiedCodes($verifyCodeInfo->order_id)) {
                ScenicOrderService::getInstance()->userConfirm($order->user_id, $order->id);
            }
        });

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
            'shop_logo',
            'shop_name',
            'payment_amount',
            'pay_time',
            'confirm_time',
            'created_at',
            'updated_at',
        ];
        $order = ScenicOrderService::getInstance()->getUserOrderById($this->userId(), $id, $columns);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }
        $ticket = ScenicOrderTicketService::getInstance()->getTicketByOrderId($order->id);
        $order['ticketInfo'] = $ticket;
        return $this->success($order);
    }
}
