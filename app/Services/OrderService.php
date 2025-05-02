<?php

namespace App\Services;

use App\Jobs\OverTimeCancelOrderJob;
use App\Jobs\PromoterConfirmJob;
use App\Models\Address;
use App\Models\CartGoods;
use App\Models\Coupon;
use App\Models\FreightTemplate;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\Shop;
use App\Utils\CodeResponse;
use App\Utils\Enums\OrderEnums;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\CreateOrderInput;
use App\Utils\Inputs\PageInput;
use App\Utils\WxMpServe;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yansongda\LaravelPay\Facades\Pay;
use Yansongda\Pay\Exceptions\GatewayException;

class OrderService extends BaseService
{
    public function getOrderListByStatus($userId, $statusList, PageInput $input, $columns = ['*'])
    {
        $query = Order::query()->where('user_id', $userId);
        if (count($statusList) != 0) {
            $query = $query->whereIn('status', $statusList);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getShopOrderList($shopId, $statusList, PageInput $input, $columns = ['*'])
    {
        $query = Order::query()->where('shop_id', $shopId);
        if (count($statusList) != 0) {
            $query = $query->whereIn('status', $statusList);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserOrderById($userId, $id, $columns = ['*'])
    {
        return Order::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getOrderById($id, $columns = ['*'])
    {
        return Order::query()->find($id, $columns);
    }

    public function getOrderListByIds(array $ids, $columns = ['*'])
    {
        return Order::query()->whereIn('id', $ids)->get($columns);
    }

    public function getUserOrderList($userId, $ids, $columns = ['*'])
    {
        return Order::query()->where('user_id', $userId)->whereIn('id', $ids)->get($columns);
    }

    public function getTodayOrderingUserCountByUserIds(array $userIds)
    {
        return Order::query()
            ->whereIn('user_id', $userIds)
            ->whereDate('created_at', Carbon::today())
            ->whereIn('status', [201, 204, 301, 401, 402, 403, 501, 502])
            ->distinct('user_id')
            ->count('user_id');
    }

    public function getUnpaidList(int $userId, array $orderIds, $columns = ['*'])
    {
        return Order::query()
            ->where('user_id', $userId)
            ->whereIn('id', $orderIds)
            ->where('status', OrderEnums::STATUS_CREATE)
            ->get($columns);
    }

    public function getUnpaidListBySn(array $orderSnList, $columns = ['*'])
    {
        return Order::query()
            ->whereIn('order_sn', $orderSnList)
            ->where('status', OrderEnums::STATUS_CREATE)
            ->get($columns);
    }

    public function getUnpaidListByIds(array $ids, $columns = ['*'])
    {
        return Order::query()
            ->whereIn('id', $ids)
            ->where('status', OrderEnums::STATUS_CREATE)
            ->get($columns);
    }

    public function getOverTimeUnpaidList($columns = ['*'])
    {
        return Order::query()
            ->where('status', OrderEnums::STATUS_CREATE)
            ->where('created_at', '<=', now()->subHours(24))
            ->get($columns);
    }

    public function getTimeoutUnConfirmOrders($columns = ['*'])
    {
        return Order::query()
            ->where('status', OrderEnums::STATUS_SHIP)
            ->where('ship_time', '<=', now()->subDays(15))
            ->where('ship_time', '>', now()->subDays(30))
            ->get($columns);
    }

    public function getPendingVerifyOrderById($id, $columns = ['*'])
    {
        return Order::query()->where('status', OrderEnums::STATUS_PENDING_VERIFICATION)->find($id, $columns);
    }

    public function getTimeoutUnFinishedOrders($columns = ['*'])
    {
        return Order::query()
            ->whereIn('status', [OrderEnums::STATUS_CONFIRM, OrderEnums::STATUS_AUTO_CONFIRM, OrderEnums::STATUS_ADMIN_CONFIRM])
            ->where('confirm_time', '<=', now()->subDays(15))
            ->where('confirm_time', '>', now()->subDays(30))
            ->get($columns);
    }

    public function generateOrderSn()
    {
        return retry(5, function () {
            $orderSn = date('YmdHis') . rand(100000, 999999);
            if ($this->isOrderSnExists($orderSn)) {
                Log::warning('当前订单号已存在，orderSn：' . $orderSn);
                $this->throwBusinessException(CodeResponse::FAIL, '订单号生成失败');
            }
            return $orderSn;
        });
    }

    public function isOrderSnExists(string $orderSn)
    {
        return Order::query()->where('order_sn', $orderSn)->exists();
    }

    public function createOrder($userId, $cartGoodsList, CreateOrderInput $input, $freightTemplateList = null, Address $address = null, Coupon $coupon = null, Shop $shopInfo = null)
    {
        $totalPrice = 0;
        $totalFreightPrice = 0;
        $couponDenomination = 0;

        /** @var CartGoods $cartGoods */
        foreach ($cartGoodsList as $cartGoods) {
            $price = bcmul($cartGoods->price, $cartGoods->number, 2);
            $totalPrice = bcadd($totalPrice, $price, 2);

            // 计算运费
            if ($input->deliveryMode == 1) {
                if ($cartGoods->freight_template_id == 0) {
                    $freightPrice = 0;
                } else {
                    $freightTemplate = $freightTemplateList->get($cartGoods->freight_template_id);
                    $freightPrice = $this->calcFreightPrice($freightTemplate, $address, $price, $cartGoods->number);
                }
                $totalFreightPrice = bcadd($totalFreightPrice, $freightPrice, 2);
            }

            // 优惠券
            if (!is_null($coupon) && $coupon->goods_id == $cartGoods->goods_id) {
                $couponDenomination = $coupon->denomination;
            }

            // 商品减库存加销量
            $row = GoodsService::getInstance()->reduceStock($cartGoods->goods_id, $cartGoods->number, $cartGoods->selected_sku_index);
            if ($row == 0) {
                $this->throwBusinessException(CodeResponse::GOODS_NO_STOCK);
            }
        }

        $paymentAmount = bcadd($totalPrice, $totalFreightPrice, 2);
        $paymentAmount = bcsub($paymentAmount, $couponDenomination, 2);

        $orderSn = $this->generateOrderSn();

        // 余额抵扣
        $deductionBalance = 0;
        if ($input->useBalance == 1) {
            $account = AccountService::getInstance()->getUserAccount($userId);
            $deductionBalance = min($paymentAmount, $account->balance);
            $paymentAmount = bcsub($paymentAmount, $deductionBalance, 2);

            // 更新余额
            AccountService::getInstance()->updateBalance($userId, 2, -$deductionBalance, $orderSn, ProductType::GOODS);
        }

        $order = Order::new();
        $order->order_sn = $orderSn;
        $order->status = OrderEnums::STATUS_CREATE;
        $order->user_id = $userId;
        $order->delivery_mode = $input->deliveryMode;
        if ($input->deliveryMode == 1) {
            $order->consignee = $address->name;
            $order->mobile = $address->mobile;
            $order->address = $address->region_desc . ' ' . $address->address_detail;
            $order->freight_price = $totalFreightPrice;
        } else {
            $order->pickup_address_id = $input->pickupAddressId;
            $order->pickup_time = $input->pickupTime;
            $order->pickup_mobile = $input->pickupMobile;
        }
        if (!is_null($shopInfo)) {
            $order->shop_id = $shopInfo->id;
            $order->shop_logo = $shopInfo->logo;
            $order->shop_name = $shopInfo->name;
        }
        $order->goods_price = $totalPrice;
        if (!is_null($coupon)) {
            $order->coupon_id = $coupon->id;
            $order->coupon_denomination = $couponDenomination;
        }
        $order->deduction_balance = $deductionBalance;
        $order->payment_amount = $paymentAmount;
        $order->refund_amount = $paymentAmount;
        $order->save();

        // 设置订单支付超时任务
        dispatch(new OverTimeCancelOrderJob($userId, $order->id));

        return $order->id;
    }

    public function calcFreightPrice(FreightTemplate $freightTemplate, Address $address, $totalPrice, $goodsNumber)
    {
        if ($freightTemplate->free_quota != 0 && $totalPrice > $freightTemplate->free_quota) {
            $freightPrice = 0;
        } else {
            $cityCode = substr(json_decode($address->region_code_list)[1], 0, 4);
            $area = collect($freightTemplate->area_list)->first(function ($area) use ($cityCode) {
                return in_array($cityCode, explode(',', $area->pickedCityCodes));
            });
            if (is_null($area)) {
                $freightPrice = 0;
            } else {
                if ($freightTemplate->compute_mode == 1) {
                    $freightPrice = $area->fee;
                } else {
                    $freightPrice = bcmul($area->fee, $goodsNumber, 2);
                }
            }
        }
        return $freightPrice;
    }

    public function createWxPayOrder($userId, array $orderIds, $openid)
    {
        $orderList = $this->getUnpaidList($userId, $orderIds);
        if (count($orderList) == 0) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '订单不存在');
        }

        $orderSnList = $orderList->pluck('order_sn')->toArray();

        $paymentAmount = 0;
        foreach ($orderList as $order) {
            $paymentAmount = bcadd($order->payment_amount, $paymentAmount, 2);
        }

        return [
            'out_trade_no' => time(),
            'body' => 'order_sn_list' . implode("','", $orderSnList),
            'attach' => json_encode($orderSnList),
            'total_fee' => bcmul($paymentAmount, 100),
            'openid' => $openid
        ];
    }

    public function wxPaySuccess(array $data)
    {
        $orderSnList = json_decode($data['attach']);
        $payId = $data['transaction_id'] ?? '';
        $actualPaymentAmount = $data['total_fee'] ? bcdiv($data['total_fee'], 100, 2) : 0;

        $orderList = $this->getUnpaidListBySn($orderSnList);

        $paymentAmount = 0;
        foreach ($orderList as $order) {
            $paymentAmount = bcadd($order->payment_amount, $paymentAmount, 2);
        }
        if (bccomp($actualPaymentAmount, $paymentAmount, 2) != 0) {
            $errMsg = "支付回调，订单{$data['body']}金额不一致，请检查，支付回调金额：{$actualPaymentAmount}，订单总金额：{$paymentAmount}";
            Log::error($errMsg);
            $this->throwBusinessException(CodeResponse::FAIL, $errMsg);
        }

        return $this->paySuccess($orderList, $payId, $actualPaymentAmount);
    }

    public function paySuccess($orderList, $payId = null, $actualPaymentAmount = null)
    {
        return DB::transaction(function () use ($actualPaymentAmount, $payId, $orderList) {
            $orderList = $orderList->map(function (Order $order) use ($actualPaymentAmount, $payId) {
                if (!is_null($payId)) {
                    $order->pay_id = $payId;
                }
                if (!is_null($actualPaymentAmount)) {
                    $order->total_payment_amount = $actualPaymentAmount;
                }
                $order->pay_time = now()->toDateTimeString();
                if ($order->delivery_mode == 1) {
                    $order->status = OrderEnums::STATUS_PAY;
                    // todo 待发货通知
                } else {
                    $order->status = OrderEnums::STATUS_PENDING_VERIFICATION;
                    OrderVerifyService::getInstance()->createVerifyCode($order->id);

                    // 同步微信后台订单发货
                    $openid = UserService::getInstance()->getUserById($order->user_id)->openid;
                    WxMpServe::new()->verify($openid, $order->pay_id);
                }

                if ($order->cas() == 0) {
                    $this->throwUpdateFail();
                }

                // todo 通知（邮件或钉钉）管理员、
                // todo 通知（短信、系统消息）商家

                return $order;
            });

            $orderIds = $orderList->pluck('id')->toArray();

            // 佣金记录状态更新为：已支付待结算
            CommissionService::getInstance()->updateListToOrderPaidStatus($orderIds, ProductType::GOODS);

            // 更新订单商品状态
            OrderGoodsService::getInstance()->updateStatusByOrderIds($orderIds, 1);

            return $orderList;
        });
    }

    public function userCancel($userId, $orderId)
    {
        return DB::transaction(function () use ($userId, $orderId) {
            $orderList = $this->getUserOrderList($userId, [$orderId]);
            if (count($orderList) == 0) {
                $this->throwBadArgumentValue();
            }
            return $this->cancel($orderList);
        });
    }

    public function systemAutoCancel($userId, $orderId)
    {
        return DB::transaction(function () use ($userId, $orderId) {
            $orderList = $this->getUserOrderList($userId, [$orderId]);
            if (count($orderList) != 0) {
                $this->cancel($orderList, 'system');
            }
        });
    }

    public function systemCancel()
    {
        return DB::transaction(function () {
            $orderList = $this->getOverTimeUnpaidList();
            if (count($orderList) != 0) {
                $this->cancel($orderList, 'system');
            }
        });
    }

    public function adminCancel($orderIds)
    {
        return DB::transaction(function () use ($orderIds) {
            $orderList = $this->getOrderListByIds($orderIds);
            if (count($orderList) == 0) {
                $this->throwBadArgumentValue();
            }
            return $this->cancel($orderList, 'admin');
        });
    }

    public function cancel($orderList, $role = 'user')
    {
        $orderList = $orderList->map(function (Order $order) use ($role) {
            if ($order->status != OrderEnums::STATUS_CREATE) {
                $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能取消');
            }
            switch ($role) {
                case 'system':
                    $order->status = OrderEnums::STATUS_AUTO_CANCEL;
                    break;
                case 'admin':
                    $order->status = OrderEnums::STATUS_ADMIN_CANCEL;
                    break;
                case 'user':
                    $order->status = OrderEnums::STATUS_CANCEL;
                    break;
            }
            $order->finish_time = now()->toDateTimeString();
            if ($order->cas() == 0) {
                $this->throwUpdateFail();
            }

            // 返还库存
            $this->returnStock($order->id);

            // 恢复优惠券
            if ($order->coupon_id != 0) {
                $this->restoreCoupon($order->user_id, $order->coupon_id);
            }

            return $order;
        });

        // 删除佣金记录
        $orderIds = $orderList->pluck('id')->toArray();
        CommissionService::getInstance()->deleteUnpaidListByOrderIds($orderIds, ProductType::GOODS);

        return $orderList;
    }

    public function returnStock($orderId)
    {
        $goodsList = OrderGoodsService::getInstance()->getListByOrderId($orderId);
        /** @var OrderGoods $goods */
        foreach ($goodsList as $goods)
        {
            $row = GoodsService::getInstance()->addStock($goods->goods_id, $goods->number, $goods->selected_sku_index);
            if ($row == 0) {
                $this->throwUpdateFail();
            }
        }
    }

    public function restoreCoupon($userId, $couponId)
    {
        $userCoupon = UserCouponService::getInstance()->getUserUsedCouponByCouponId($userId, $couponId);
        if (!is_null($userCoupon)) {
            $userCoupon->status = 1;
            $userCoupon->save();
        }
        return $userCoupon;
    }

    public function importOrders(array $row)
    {
        $validator = Validator::make($row, [
            'order_id' => 'required|integer',
            'ship_channel' => 'required|string',
            'ship_code' => 'string',
            'ship_sn' => 'required|string',
        ]);
        if ($validator->fails()) {
            $this->throwBusinessException(CodeResponse::PARAM_VALUE_INVALID, $validator->errors());
        }
        $this->ship($row['order_id'], $row['ship_channel'], $row['ship_code'], $row['ship_sn']);
    }

    public function ship($orderId, $shipChannel, $shipCode, $shipSn)
    {
        $order = $this->getOrderById($orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canShipHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单未付款，无法发货');
        }

        if (empty($shipCode)) {
            $express = ExpressService::getInstance()->getExpressByName($shipChannel);
            $shipCode = $express->code;
        }

        DB::transaction(function () use ($order, $shipChannel, $shipCode, $shipSn) {
            $order->status = OrderEnums::STATUS_SHIP;
            $order->ship_time = now()->toDateTimeString();
            if ($order->cas() == 0) {
                $this->throwUpdateFail();
            }

            $orderPackage = OrderPackageService::getInstance()->create($order->id, $shipChannel, $shipCode, $shipSn);
            $orderGoodsList = OrderGoodsService::getInstance()->getListByOrderId($order->id);
            foreach ($orderGoodsList as $orderGoods) {
                OrderPackageGoodsService::getInstance()
                    ->create($order->id, $orderPackage->id, $orderGoods->goods_id, $orderGoods->cover, $orderGoods->name, $orderGoods->number);
            }

            // 发货同步小程序后台
            if ($order->refund_amount != 0) {
                $openid = UserService::getInstance()->getUserById($order->user_id)->openid;
                WxMpServe::new()->uploadShippingInfo($openid, $order, [$orderPackage], true);
            }

            // todo 待发货通知
        });

        return $order;
    }

    public function splitShip($orderId, array $packageList, $isAllDelivered = false)
    {
        $order = $this->getOrderById($orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canShipHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单未付款，无法发货');
        }

        DB::transaction(function () use ($order, $packageList, $isAllDelivered) {
            if ($isAllDelivered) {
                $order->status = OrderEnums::STATUS_SHIP;
                $order->ship_time = now()->toDateTimeString();
                if ($order->cas() == 0) {
                    $this->throwUpdateFail();
                }
            }

            $orderPackageList = [];
            foreach ($packageList as $package) {
                $shipChannel = $package['shipChannel'];
                $shipCode = $package['shipCode'];
                $shipSn = $package['shipSn'];
                if (empty($shipCode)) {
                    $express = ExpressService::getInstance()->getExpressByName($shipChannel);
                    $shipCode = $express->code;
                }
                $orderPackage = OrderPackageService::getInstance()->create($order->id, $shipChannel, $shipCode, $shipSn);
                $orderPackageList[] = $orderPackage;

                $goodsList = json_decode($package['goodsList']);
                foreach ($goodsList as $goods) {
                    OrderPackageGoodsService::getInstance()
                        ->create($order->id, $orderPackage->id, $goods->goodsId, $goods->cover, $goods->name, $goods->number);
                }
            }

            // 发货同步小程序后台
            if ($order->refund_amount != 0) {
                $openid = UserService::getInstance()->getUserById($order->user_id)->openid;
                WxMpServe::new()->uploadShippingInfo($openid, $order, $orderPackageList, $isAllDelivered);
            }

            // todo 待发货通知
        });

        return $order;
    }

    public function userConfirm($userId, $orderId)
    {
        $orderList = $this->getUserOrderList($userId, [$orderId]);
        if (count($orderList) == 0) {
            $this->throwBadArgumentValue();
        }
        return $this->confirm($orderList);
    }

    public function adminConfirm($orderIds)
    {
        return DB::transaction(function () use ($orderIds) {
            $orderList = $this->getOrderListByIds($orderIds);
            if (count($orderList) == 0) {
                $this->throwBadArgumentValue();
            }
            return $this->confirm($orderList, 'admin');
        });
    }

    public function systemConfirm()
    {
        return DB::transaction(function () {
            $orderList = $this->getTimeoutUnConfirmOrders();
            if (count($orderList) != 0) {
                $this->confirm($orderList, 'system');
            }
        });
    }

    public function confirm($orderList, $role = 'user')
    {
        // 订单确认时，如果存在普通用户购买礼包的逻辑，需要执行生成推广员的逻辑
        // 如果是用户手动确认，则需根据订单商品是否支持7天无理由，延迟生成推广员身份（佣金逻辑同理）
        $orderIds = $orderList->pluck('id')->toArray();
        $orderGoodsList = OrderGoodsService::getInstance()->getListByOrderIds($orderIds)->keyBy('order_id');

        $orderList = $orderList->map(function (Order $order) use ($role, $orderGoodsList) {
            if (!$order->canConfirmHandle()) {
                $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单无法确认');
            }
            switch ($role) {
                case 'system':
                    $order->status = OrderEnums::STATUS_AUTO_CONFIRM;
                    break;
                case 'admin':
                    $order->status = OrderEnums::STATUS_ADMIN_CONFIRM;
                    break;
                case 'user':
                    $order->status = OrderEnums::STATUS_CONFIRM;
                    break;
            }
            $order->confirm_time = now()->toDateTimeString();
            if ($order->cas() == 0) {
                $this->throwUpdateFail();
            }

            /** @var OrderGoods $orderGoods */
            $orderGoods = $orderGoodsList->get($order->id);
            if ($orderGoods->is_gift == 1 && $orderGoods->user_level == 0) {
                if ($orderGoods->refund_status == 1 && $role == 'user') {
                    // 7天无理由商品：确认收货7天后生成推广员身份
                    dispatch(new PromoterConfirmJob($orderGoods->id));
                } else {
                    PromoterService::getInstance()->createPromoterByGift($orderGoods->id);
                }
            }

            return $order;
        });

        // 佣金记录变更为待提现
        CommissionService::getInstance()->updateListToOrderConfirmStatus($orderIds, ProductType::GOODS, $role);

        return $orderList;
    }

    public function systemFinish()
    {
        $orderList = $this->getTimeoutUnFinishedOrders();
        if (count($orderList) != 0) {
            $orderList->map(function (Order $order) {
                if (!$order->canFinishHandle()) {
                    $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能设置为完成状态');
                }
                $order->status = OrderEnums::STATUS_AUTO_FINISHED;
                if ($order->cas() == 0) {
                    $this->throwUpdateFail();
                }
            });

            // todo 商品默认好评
        }
    }

    public function finish($userId, $orderId)
    {
        $order = $this->getUserOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canFinishHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能设置为完成状态');
        }
        $order->status = OrderEnums::STATUS_FINISHED;
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }
        return $order;
    }

    public function userRefund($userId, $orderId)
    {
        $order = $this->getUserOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        $this->refund($order);
    }

    public function adminRefund($orderIds)
    {
        $orderList = $this->getOrderListByIds($orderIds);
        if (count($orderList) == 0) {
            $this->throwBadArgumentValue();
        }
        foreach ($orderList as $order) {
            $this->refund($order);
        }
    }

    public function refund(Order $order)
    {
        if (!$order->canRefundHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '该订单不能申请退款');
        }
        DB::transaction(function () use ($order) {
            try {
                // 微信退款
                if ($order->refund_amount != 0) {
                    $refundParams = [
                        'transaction_id' => $order->pay_id,
                        'out_refund_no' => time(),
                        'total_fee' => bcmul($order->total_payment_amount, 100),
                        'refund_fee' => bcmul($order->refund_amount, 100),
                        'refund_desc' => '商品退款',
                        'type' => 'miniapp'
                    ];

                    $result = Pay::wechat()->refund($refundParams);
                    $order->refund_id = $result['refund_id'];
                    Log::info('order_wx_refund', $result->toArray());
                }

                $order->status = OrderEnums::STATUS_REFUND_CONFIRM;
                $order->refund_time = now()->toDateTimeString();
                if ($order->cas() == 0) {
                    $this->throwUpdateFail();
                }

                // 退还余额
                if ($order->deduction_balance != 0) {
                    AccountService::getInstance()
                        ->updateBalance($order->user_id, 3, $order->deduction_balance, $order->order_sn, ProductType::GOODS);
                }

                // 删除佣金记录
                CommissionService::getInstance()->deletePaidListByOrderIds([$order->id], ProductType::GOODS);

                // 更新订单商品状态
                OrderGoodsService::getInstance()->updateStatusByOrderIds([$order->id], 2);
            } catch (GatewayException $exception) {
                Log::error('wx_refund_fail', [$exception]);
            }
        });
    }

    public function afterSale($userId, $orderId)
    {
        $order = $this->getUserOrderById($userId, $orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canAftersaleHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '该订单无法申请售后');
        }
        $order->status = OrderEnums::STATUS_REFUND;
        if ($order->cas() == 0) {
            $this->throwUpdateFail();
        }
        return $order;
    }

    public function afterSaleRefund($orderId, $goodsId, $couponId, $refundAmount)
    {
        $order = $this->getOrderById($orderId);
        if (is_null($order)) {
            $this->throwBadArgumentValue();
        }
        if (!$order->canRefundHandle()) {
            $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '该订单不支持退款');
        }

        /** @var OrderGoods $orderGoods */
        $orderGoods = OrderGoodsService::getInstance()->getOrderGoods($orderId, $goodsId);
        $totalPrice = bcmul($orderGoods->price, $orderGoods->number, 2);
        $couponDenomination = 0;
        if ($couponId != 0) {
            $coupon = CouponService::getInstance()->getGoodsCoupon($couponId, $goodsId);
            if (!is_null($coupon)) {
                $couponDenomination = $coupon->denomination;
            }
        }
        $actualRefundAmount = bcsub($totalPrice, $couponDenomination, 2);

        if (bccomp($actualRefundAmount, $refundAmount, 2) != 0) {
            $errMsg = "退款申请，订单id为{$orderId}商品id为{$goodsId}，退款金额（{$refundAmount}）与实际可退款金额（{$actualRefundAmount}）不一致";
            Log::error($errMsg);
            $this->throwBusinessException(CodeResponse::FAIL, $errMsg);
        }

        try {
            $refundParams = [
                'transaction_id' => $order->pay_id,
                'out_refund_no' => time(),
                'total_fee' => bcmul($order->payment_amount, 100),
                'refund_fee' => bcmul($actualRefundAmount, 100),
                'refund_desc' => '商品退款',
                'type' => 'miniapp'
            ];

            $result = Pay::wechat()->refund($refundParams);
            Log::info('order_wx_refund', $result->toArray());

            $order->status = OrderEnums::STATUS_REFUND_CONFIRM;
            $order->refund_id = $result['refund_id'];
            $order->refund_time = now()->toDateTimeString();
            if ($order->cas() == 0) {
                $this->throwUpdateFail();
            }

            // 退还余额
            if ($order->deduction_balance != 0) {
                AccountService::getInstance()
                    ->updateBalance($order->user_id, 3, $order->deduction_balance, $order->order_sn, ProductType::GOODS);
            }

            // 删除佣金记录
            CommissionService::getInstance()->deletePaidCommission($order->id, ProductType::GOODS, $goodsId);

            // 更新订单商品状态
            OrderGoodsService::getInstance()->updateStatusByOrderIds([$order->id], 2);
        } catch (GatewayException $exception) {
            Log::error('wx_refund_fail', [$exception]);
        }
    }

    public function delete($orderList)
    {
        foreach ($orderList as $order) {
            if (!$order->canDeleteHandle()) {
                $this->throwBusinessException(CodeResponse::ORDER_INVALID_OPERATION, '订单不能删除');
            }
            OrderGoodsService::getInstance()->delete($order->id);
            $order->delete();
        }
    }
}
