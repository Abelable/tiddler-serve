<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Catering\SetMealOrder;
use App\Models\OrderSetMeal;
use App\Services\AccountService;
use App\Services\CommissionService;
use App\Services\Mall\Catering\CateringShopManagerService;
use App\Services\Mall\Catering\RestaurantManagerService;
use App\Services\OrderSetMealService;
use App\Services\PromoterService;
use App\Services\RelationService;
use App\Services\RestaurantService;
use App\Services\SetMealOrderService;
use App\Services\SetMealService;
use App\Services\SetMealVerifyService;
use App\Utils\CodeResponse;
use App\Utils\Enums\SetMealOrderStatus;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\SetMealOrderInput;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yansongda\LaravelPay\Facades\Pay;

class SetMealOrderController extends Controller
{
    public function paymentAmount()
    {
        $setMealId = $this->verifyRequiredId('setMealId');
        $num = $this->verifyRequiredInteger('num');
        $useBalance = $this->verifyBoolean('useBalance', false);

        $setMeal = SetMealService::getInstance()->getSetMealById($setMealId);
        $totalPrice = (float)bcmul($setMeal->price, $num, 2);

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
        /** @var SetMealOrderInput $input */
        $input = SetMealOrderInput::new();

        // 分布式锁，防止重复请求
        $lockKey = sprintf('create_set_meal_order_%s_%s', $this->userId(), md5(serialize($input)));
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

        $setMeal = SetMealService::getInstance()->getSetMealById($input->setMealId);
        $paymentAmount = (float)bcmul($setMeal->price, $input->num, 2);

        $orderId = DB::transaction(function () use ($upperSuperiorLevel, $upperSuperiorId, $superiorLevel, $superiorId, $userLevel, $userId, $paymentAmount, $setMeal, $input) {
            $order = SetMealOrderService::getInstance()->createOrder($this->user(), $input, $setMeal->provider_id, $paymentAmount);

            // 生成核销码
            SetMealVerifyService::getInstance()->createVerifyCode($order->id, $input->restaurantId);

            // 生成订单套餐快照
            OrderSetMealService::getInstance()->createOrderSetMeal($order->id, $input->num, $setMeal);

            // 生成佣金记录
            CommissionService::getInstance()
                ->createSetMealCommission($order->id, $order->order_sn, $setMeal, $paymentAmount, $userId, $userLevel, $superiorId, $superiorLevel, $upperSuperiorId, $upperSuperiorLevel);

            // 增加餐馆、套餐销量
            RestaurantService::getInstance()->increaseSalesVolume($input->restaurantId, $input->num);
            $setMeal->sales_volume = $setMeal->sales_volume + $input->num;
            $setMeal->save();

            return $order->id;
        });

        return $this->success($orderId);
    }

    public function payParams()
    {
        $orderId = $this->verifyRequiredInteger('orderId');
        $order = SetMealOrderService::getInstance()->createWxPayOrder($this->userId(), $orderId, $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($order);
        return $this->success($payParams);
    }

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');

        $statusList = $this->statusList($status);
        $page = SetMealOrderService::getInstance()->getOrderListByStatus($this->userId(), $statusList, $input);
        $list = $this->orderList($page);

        return $this->success($this->paginate($page, $list));
    }

    public function providerList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');

        $statusList = $this->statusList($status);
        $page = SetMealOrderService::getInstance()->getProviderOrderList($this->user()->cateringProvider->id, $statusList, $input);
        $list = $this->orderList($page);

        return $this->success($this->paginate($page, $list));
    }

    private function statusList($status) {
        switch ($status) {
            case 1:
                $statusList = [SetMealOrderStatus::CREATED];
                break;
            case 2:
                $statusList = [SetMealOrderStatus::PAID];
                break;
            case 3:
                $statusList = [SetMealOrderStatus::CONFIRMED, SetMealOrderStatus::AUTO_CONFIRMED];
                break;
            case 4:
                $statusList = [SetMealOrderStatus::REFUNDING, SetMealOrderStatus::REFUNDED];
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
        $setMealList = OrderSetMealService::getInstance()->getListByOrderIds($orderIds)->keyBy('order_id');
        return $orderList->map(function (SetMealOrder $order) use ($setMealList) {
            /** @var OrderSetMeal $setMeal */
            $setMeal = $setMealList->get($order->id);
            $setMeal->package_details = json_decode($setMeal->package_details);
            $setMeal->use_time_list = json_decode($setMeal->use_time_list) ?: [];
            $setMeal->use_rules = json_decode($setMeal->use_rules) ?: [];

            return [
                'id' => $order->id,
                'status' => $order->status,
                'statusDesc' => SetMealOrderStatus::TEXT_MAP[$order->status],
                'setMealInfo' => $setMeal,
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
        SetMealOrderService::getInstance()->userCancel($this->userId(), $id);
        return $this->success();
    }

    public function verifyCode()
    {
        $orderId = $this->verifyRequiredId('orderId');
        $restaurantId = $this->verifyRequiredId('hotelId');

        $verifyCodeInfo = SetMealVerifyService::getInstance()->getVerifyCodeInfo($orderId, $restaurantId);
        if (is_null($verifyCodeInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '核销信息不存在');
        }

        return $this->success($verifyCodeInfo->code);
    }

    public function verify()
    {
        $code = $this->verifyRequiredString('code');

        $verifyCodeInfo = SetMealVerifyService::getInstance()->getByCode($code);
        if (is_null($verifyCodeInfo)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '无效核销码');
        }

        $order = SetMealOrderService::getInstance()->getPaidOrderById($verifyCodeInfo->order_id);
        if (is_null($order)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '订单不存在');
        }

        $managerIds = RestaurantManagerService::getInstance()
            ->getListByRestaurantId($verifyCodeInfo->restaurant_id)
            ->pluck('manager_id')
            ->toArray();
        $managerUserIds = array_unique(CateringShopManagerService::getInstance()
            ->getListByIds($managerIds)->pluck('user_id')->toArray());
        if ($order->shop_id != $this->user()->cateringShop->id && !in_array($this->userId(), $managerUserIds)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '非当前餐馆核销员，无法核销');
        }

        DB::transaction(function () use ($verifyCodeInfo, $order) {
            SetMealVerifyService::getInstance()->verify($verifyCodeInfo, $this->userId());
            SetMealOrderService::getInstance()->userConfirm($order->user_id, $order->id);
        });

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        DB::transaction(function () use ($id) {
            SetMealOrderService::getInstance()->delete($this->userId(), $id);
        });
        return $this->success();
    }

    public function refund()
    {
        $id = $this->verifyRequiredId('id');
        SetMealOrderService::getInstance()->userRefund($this->userId(), $id);
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
        $order = SetMealOrderService::getInstance()->getOrderById($this->userId(), $id, $columns);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }

        $setMeal = OrderSetMealService::getInstance()->getSetMealByOrderId($order->id);
        unset($setMeal->id);
        $setMeal->package_details = json_decode($setMeal->package_details);
        $setMeal->use_time_list = json_decode($setMeal->use_time_list) ?: [];
        $setMeal->use_rules = json_decode($setMeal->use_rules) ?: [];
        $order['setMealInfo'] = $setMeal;

        return $this->success($order);
    }
}
