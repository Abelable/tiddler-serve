<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\CartGoods;
use App\Models\Coupon;
use App\Models\FreightTemplate;
use App\Models\Order;
use App\Models\Shop;
use App\Services\AccountService;
use App\Services\AddressService;
use App\Services\CartGoodsService;
use App\Services\CommissionService;
use App\Services\CouponService;
use App\Services\FreightTemplateService;
use App\Services\OrderGoodsService;
use App\Services\OrderPackageService;
use App\Services\OrderService;
use App\Services\OrderVerifyService;
use App\Services\PromoterService;
use App\Services\RelationService;
use App\Services\ShopIncomeService;
use App\Services\ShopPickupAddressService;
use App\Services\ShopService;
use App\Services\UserCouponService;
use App\Utils\CodeResponse;
use App\Utils\Enums\OrderStatus;
use App\Utils\Inputs\CreateOrderInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yansongda\LaravelPay\Facades\Pay;

class OrderController extends Controller
{
    public function preOrderInfo()
    {
        $cartGoodsIds = $this->verifyArrayNotEmpty('cartGoodsIds');
        $deliveryMode = $this->verifyRequiredInteger('deliveryMode');
        $addressId = $this->verifyId('addressId');
        $couponId = $this->verifyId('couponId');
        $useBalance = $this->verifyBoolean('useBalance', false);

        $cartGoodsListColumns = ['shop_id', 'goods_id', 'is_gift', 'delivery_mode', 'freight_template_id', 'cover', 'name', 'selected_sku_name', 'price', 'number'];
        $cartGoodsList = CartGoodsService::getInstance()->getCartGoodsListByIds($this->userId(), $cartGoodsIds, $cartGoodsListColumns);

        $address = null;
        if ($deliveryMode == 1) {
            $addressColumns = ['id', 'name', 'mobile', 'region_code_list', 'region_desc', 'address_detail'];
            if (is_null($addressId)) {
                /** @var Address $address */
                $address = AddressService::getInstance()->getDefaultAddress($this->userId(), $addressColumns);
            } else {
                /** @var Address $address */
                $address = AddressService::getInstance()->getById($this->userId(), $addressId, $addressColumns);
            }

            $freightTemplateIds = $cartGoodsList->pluck('freight_template_id')->toArray();
            $freightTemplateList = FreightTemplateService::getInstance()
                ->getListByIds($freightTemplateIds)
                ->map(function (FreightTemplate $freightTemplate) {
                    $freightTemplate->area_list = json_decode($freightTemplate->area_list);
                    return $freightTemplate;
                })->keyBy('id');
        }

        $errMsg = '';
        $totalFreightPrice = 0;
        $couponDenomination = 0;
        $deductionBalance = 0;
        $totalPrice = 0;
        $totalNumber = 0;

        // 优惠券逻辑
        $couponList = $this->getCouponList($cartGoodsList);
        if (count($couponList) != 0) {
            if (is_null($couponId)) {
                $couponDenomination = $couponList->first()->denomination;
            } else if ($couponId != 0) {
                $couponDenomination = $couponList->keyBy('id')->get($couponId)->denomination;
            }
        }

        foreach ($cartGoodsList as $cartGoods) {
            $price = bcmul($cartGoods->price, $cartGoods->number, 2);
            $totalPrice = bcadd($totalPrice, $price, 2);
            $totalNumber = $totalNumber + $cartGoods->number;

            // 计算运费
            if ($deliveryMode == 1) {
                if (is_null($address) || $cartGoods->freight_template_id == 0) {
                    $freightPrice = 0;
                } else {
                    /** @var FreightTemplate $freightTemplate */
                    $freightTemplate = $freightTemplateList->get($cartGoods->freight_template_id);
                    if ($freightTemplate->free_quota != 0 && $price > $freightTemplate->free_quota) {
                        $freightPrice = 0;
                    } else {
                        $cityCode = json_decode($address->region_code_list)[1];
                        if (strlen($cityCode) != 6) {
                            $errMsg = '收货地址异常，请编辑更新地址，建议手动获取地址省市区';
                            $freightPrice = 0;
                        } else {
                            $area = collect($freightTemplate->area_list)->first(function ($area) use ($cityCode) {
                                return in_array(substr($cityCode, 0, 4), explode(',', $area->pickedCityCodes));
                            });
                            if (is_null($area)) {
                                $errMsg = '商品"' . $cartGoods->name . '"暂不支持配送至当前地址，请更换收货地址';
                                $freightPrice = 0;
                            } else {
                                if ($freightTemplate->compute_mode == 1) {
                                    $freightPrice = $area->fee;
                                } else {
                                    $freightPrice = bcmul($area->fee, $cartGoods->number, 2);
                                }
                            }
                        }
                    }
                }
                $totalFreightPrice = bcadd($totalFreightPrice, $freightPrice, 2);
            }
        }

        $paymentAmount = bcadd($totalPrice, $totalFreightPrice, 2);
        $paymentAmount = bcsub($paymentAmount, $couponDenomination, 2);

        // 余额逻辑
        $account = AccountService::getInstance()->getUserAccount($this->userId());
        $accountBalance = $account->status == 1 ? $account->balance : 0;
        if ($useBalance) {
            $deductionBalance = min($paymentAmount, $accountBalance);
            $paymentAmount = bcsub($paymentAmount, $deductionBalance, 2);
        }

        $shopIds = array_unique($cartGoodsList->pluck('shop_id')->toArray());
        $shopList = ShopService::getInstance()->getShopListByIds($shopIds, ['id', 'logo', 'name']);
        $goodsLists = $shopList->map(function (Shop $shop) use ($cartGoodsList) {
            return [
                'shopInfo' => $shop,
                'goodsList' => $cartGoodsList->filter(function (CartGoods $cartGoods) use ($shop) {
                    return $cartGoods->shop_id == $shop->id;
                })->map(function (CartGoods $cartGoods) {
                    unset($cartGoods->shop_id);
                    return $cartGoods;
                })
            ];
        });
        if (in_array(0, $shopIds)) {
            $goodsLists->prepend([
                'goodsList' => $cartGoodsList->filter(function (CartGoods $cartGoods) {
                    return $cartGoods->shop_id == 0;
                })->map(function (CartGoods $cartGoods) {
                    unset($cartGoods->shop_id);
                    return $cartGoods;
                })
            ]);
        }

        return $this->success([
            'errMsg' => $errMsg,
            'addressInfo' => $address,
            'goodsLists' => $goodsLists,
            'freightPrice' => $totalFreightPrice,
            'couponList' => $couponList,
            'couponDenomination' => $couponDenomination,
            'totalPrice' => $totalPrice,
            'totalNumber' => $totalNumber,
            'accountBalance' => $accountBalance,
            'deductionBalance' => $deductionBalance,
            'paymentAmount' => $paymentAmount
        ]);
    }

    private function getCouponList($cartGoodsList)
    {
        $couponIds = UserCouponService::getInstance()->getUserCouponList($this->userId())->pluck('coupon_id')->toArray();
        $couponList = CouponService::getInstance()->getAvailableCouponListByIds($couponIds)->keyBy('goods_id');
        return $cartGoodsList->map(function (CartGoods $cartGoods) use ($couponList) {
            /** @var Coupon $coupon */
            $coupon = $couponList->get($cartGoods->goods_id);
            if (!is_null($coupon)) {
                switch ($coupon->type) {
                    case 1:
                        return $coupon;
                    case 2:
                        if ($cartGoods->number >= $coupon->num_limit) {
                            return $coupon;
                        } else {
                            return null;
                        }
                    case 3:
                        if (bcmul($cartGoods->price, $cartGoods->number, 2) >= $coupon->price_limit) {
                            return $coupon;
                        } else {
                            return null;
                        }
                }
            }
            return null;
        })->filter()->sortBy('denomination');
    }

    public function submit()
    {
        /** @var CreateOrderInput $input */
        $input = CreateOrderInput::new();

        // 分布式锁，防止重复请求
        $lockKey = sprintf('create_order_%s_%s', $this->userId(), md5(serialize($input)));
        $lock = Cache::lock($lockKey, 5);
        if (!$lock->get()) {
            $this->fail(CodeResponse::FAIL, '请勿重复提交订单');
        }

        $orderIds = DB::transaction(function () use ($input) {
            // 1.获取地址
            $address = null;
            if ($input->deliveryMode == 1) {
                $address = AddressService::getInstance()->getById($this->userId(), $input->addressId);
                if (is_null($address)) {
                    return $this->fail(CodeResponse::NOT_FOUND, '用户地址不存在');
                }
            }

            // 2.获取优惠券
            $coupon = null;
            if (!is_null($input->couponId) && $input->couponId != 0) {
                $userCoupon = UserCouponService::getInstance()->getUserCoupon($this->userId(), $input->couponId);
                if (is_null($userCoupon)) {
                    return $this->fail(CodeResponse::NOT_FOUND, '优惠券无法使用');
                }
                $coupon = CouponService::getInstance()->getAvailableCouponById($input->couponId);
                if (is_null($coupon)) {
                    return $this->fail(CodeResponse::NOT_FOUND, '优惠券不存在');
                }
            }

            // 3.判断余额状态
            if (!is_null($input->useBalance) && $input->useBalance != 0) {
                $account = AccountService::getInstance()->getUserAccount($this->userId());
                if ($account->status == 0 || $account->balance <= 0) {
                    return $this->fail(CodeResponse::NOT_FOUND, '余额异常不可用，请联系客服解决问题');
                }
            }

            // 4.获取购物车商品
            $cartGoodsList = CartGoodsService::getInstance()->getCartGoodsListByIds($this->userId(), $input->cartGoodsIds);

            // 5.获取运费模板列表
            $freightTemplateList = null;
            if ($input->deliveryMode == 1) {
                $freightTemplateIds = $cartGoodsList->pluck('freight_template_id')->toArray();
                $freightTemplateList = FreightTemplateService::getInstance()
                    ->getListByIds($freightTemplateIds)
                    ->map(function (FreightTemplate $freightTemplate) {
                        $freightTemplate->area_list = json_decode($freightTemplate->area_list);
                        return $freightTemplate;
                    })->keyBy('id');
            }


            $promoterInfo = $this->user()->promoterInfo;
            $promoterStatus = $promoterInfo ? $promoterInfo->status : 0;
            $userId = $this->userId();
            $userLevel = $promoterInfo ? $promoterInfo->level : 0;
            $superiorId = RelationService::getInstance()->getSuperiorId($userId);
            $superiorLevel = PromoterService::getInstance()->getPromoterLevel($superiorId);
            $upperSuperiorId = RelationService::getInstance()->getSuperiorId($superiorId);
            $upperSuperiorLevel = PromoterService::getInstance()->getPromoterLevel($upperSuperiorId);

            // 6.按店铺进行订单拆分，生成对应订单
            $shopIds = array_unique($cartGoodsList->pluck('shop_id')->toArray());
            $shopList = ShopService::getInstance()->getShopListByIds($shopIds);

            $orderIds = $shopList
                ->map(function (Shop $shop) use ($promoterStatus, $upperSuperiorLevel, $upperSuperiorId, $superiorLevel, $superiorId, $userLevel, $input, $userId, $address, $cartGoodsList, $freightTemplateList, $coupon) {
                $filterCartGoodsList = $cartGoodsList->filter(function (CartGoods $cartGoods) use ($shop) {
                    return $cartGoods->shop_id == $shop->id;
                });

                // 7.生成订单
                $order = OrderService::getInstance()
                    ->createOrder($userId, $filterCartGoodsList, $input, $freightTemplateList, $address, $coupon, $shop);

                // 8.生成订单商品快照
                OrderGoodsService::getInstance()->createList($filterCartGoodsList, $order->id, $userId, $userLevel, $promoterStatus);

                foreach ($filterCartGoodsList as $cartGoods) {
                    // 9.生成佣金记录
                    if (!$cartGoods->is_gift || ($cartGoods->is_gift && $userLevel != 0)) {
                        CommissionService::getInstance()
                            ->createGoodsCommission($order->id, $order->order_sn, $cartGoods, $userId, $userLevel, $superiorId, $superiorLevel, $upperSuperiorId, $upperSuperiorLevel, $coupon);
                    }

                    // 10.生成店铺收益
                    ShopIncomeService::getInstance()->createIncome($shop->id, $order->id, $order->order_sn, $cartGoods, $coupon);
                }

                return $order->id;
            });
            if (in_array(0, $shopIds)) {
                $filterCartGoodsList = $cartGoodsList->filter(function (CartGoods $cartGoods) {
                    return $cartGoods->shop_id == 0;
                });

                // 7.生成订单
                $order = OrderService::getInstance()
                    ->createOrder($userId, $filterCartGoodsList, $input, $freightTemplateList, $address, $coupon);

                // 8.生成订单商品快照
                OrderGoodsService::getInstance()->createList($filterCartGoodsList, $order->id, $userId, $userLevel, $promoterStatus);

                foreach ($filterCartGoodsList as $cartGoods) {
                    // 9.生成佣金记录
                    if (!$cartGoods->is_gift || ($cartGoods->is_gift && $userLevel != 0)) {
                        CommissionService::getInstance()
                            ->createGoodsCommission($order->id, $order->order_sn, $cartGoods, $userId, $userLevel, $superiorId, $superiorLevel, $upperSuperiorId, $upperSuperiorLevel, $coupon);
                    }
                }

                $orderIds->push($order->id);
            }

            // 11.清空购物车
            CartGoodsService::getInstance()->deleteCartGoodsList($this->userId(), $input->cartGoodsIds);

            // 12.使用优惠券
            if (!is_null($input->couponId)) {
                UserCouponService::getInstance()->useCoupon($this->userId(), $input->couponId);
            }

            return $orderIds;
        });

        return $this->success($orderIds);
    }

    public function payParams()
    {
        $orderIds = $this->verifyArrayNotEmpty('orderIds');
        $order = OrderService::getInstance()->createWxPayOrder($this->userId(), $orderIds, $this->user()->openid);
        $payParams = null;
        if ($order['total_fee'] == 0) {
            $orderList = OrderService::getInstance()->getUnpaidListByIds($orderIds);
            OrderService::getInstance()->paySuccess($orderList);
        } else {
            $payParams = Pay::wechat()->miniapp($order);
        }
        return $this->success($payParams);
    }

    public function total()
    {
        return $this->success([
            OrderService::getInstance()->getTotal($this->userId(), $this->statusList(1)),
            OrderService::getInstance()->getTotal($this->userId(), $this->statusList(2)),
            OrderService::getInstance()->getTotal($this->userId(), $this->statusList(3)),
            OrderService::getInstance()->getTotal($this->userId(), $this->statusList(4)),
            OrderService::getInstance()->getTotal($this->userId(), [OrderStatus::REFUNDING]),
        ]);
    }

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');

        $statusList = $this->statusList($status);
        $page = OrderService::getInstance()->getOrderListByStatus($this->userId(), $statusList, $input);
        $orderList = collect($page->items());
        $list = $this->handleOrderList($orderList);

        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        $keywords = $this->verifyRequiredString('keywords');

        $orderGoodsList = OrderGoodsService::getInstance()->searchList($this->userId(), $keywords);
        $orderIds = $orderGoodsList->pluck('order_id')->toArray();
        $orderList = OrderService::getInstance()->getOrderListByIds($orderIds);
        $list = $this->handleOrderList($orderList);

        return $this->success($list);
    }

    private function statusList($status) {
        switch ($status) {
            case 1:
                $statusList = [OrderStatus::CREATED];
                break;
            case 2:
                $statusList = [OrderStatus::PAID, OrderStatus::EXPORTED];
                break;
            case 3:
                $statusList = [OrderStatus::SHIPPED, OrderStatus::PENDING_VERIFICATION];
                break;
            case 4:
                $statusList = [OrderStatus::CONFIRMED, OrderStatus::AUTO_CONFIRMED, OrderStatus::ADMIN_CONFIRMED];
                break;
            case 5:
                $statusList = [OrderStatus::REFUNDING, OrderStatus::REFUNDED];
                break;
            case 6:
                $statusList = [OrderStatus::FINISHED];
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
        $goodsListColumns = ['order_id', 'goods_id', 'cover', 'name', 'selected_sku_name', 'price', 'number'];
        $groupedGoodsList = OrderGoodsService::getInstance()
            ->getListByOrderIds($orderIds, $goodsListColumns)->groupBy('order_id');
        return $orderList->map(function (Order $order) use ($groupedGoodsList) {
            $goodsList = $groupedGoodsList->get($order->id);
            return [
                'id' => $order->id,
                'status' => $order->status,
                'statusDesc' => OrderStatus::TEXT_MAP[$order->status],
                'shopId' => $order->shop_id,
                'shopLogo' => $order->shop_logo,
                'shopName' => $order->shop_name,
                'goodsList' => $goodsList,
                'payTime' => $order->pay_time,
                'paymentAmount' => $order->payment_amount,
                'deliveryMode' => $order->delivery_mode,
                'consignee' => $order->consignee,
                'mobile' => $order->mobile,
                'address' => $order->address,
                'orderSn' => $order->order_sn,
                'createdAt' => $order->created_at,
            ];
        });
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
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
            'shop_id',
            'shop_logo',
            'shop_name',
            'goods_price',
            'freight_price',
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
        $order = OrderService::getInstance()->getUserOrder($this->userId(), $id, $columns);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }
        $goodsList = OrderGoodsService::getInstance()->getListByOrderId($order->id);
        $order['goods_list'] = $goodsList;

        $packageList = OrderPackageService::getInstance()->getListByOrderId($order->id);
        $order['package_list'] = $packageList ?: [];

        if ($order->delivery_mode == 2) {
            $pickupAddress = ShopPickupAddressService::getInstance()
                ->getAddressById($order->pickup_address_id, ['id', 'name', 'address_detail', 'latitude', 'longitude']);
            $order['pickup_address'] = $pickupAddress;
            unset($order['pickup_address_id']);

            if ($order->status !== OrderStatus::CREATED) {
                $verifyInfo = OrderVerifyService::getInstance()->getByOrderId($order->id);
                $order['verify_code'] = $verifyInfo->code ?: null;
            }
        }

        return $this->success($order);
    }

    public function verifyCode()
    {
        $orderId = $this->verifyRequiredId('orderId');

        $verifyCodeInfo = OrderVerifyService::getInstance()->getByOrderId($orderId);
        if (is_null($verifyCodeInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '核销信息不存在');
        }

        return $this->success($verifyCodeInfo->code);
    }

    public function confirm()
    {
        $id = $this->verifyRequiredId('id');
        DB::transaction(function () use ($id) {
            OrderService::getInstance()->userConfirm($this->userId(), $id);
        });
        return $this->success();
    }

    public function cancel()
    {
        $id = $this->verifyRequiredId('id');
        OrderService::getInstance()->userCancel($this->userId(), $id);
        return $this->success();
    }

    public function delete()
    {
        $ids = $this->verifyArrayNotEmpty('ids', []);
        $orderList = OrderService::getInstance()->getUserOrderList($this->userId(), $ids);
        if (count($orderList) == 0) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '订单不存在');
        }
        DB::transaction(function () use ($orderList) {
            OrderService::getInstance()->delete($orderList);
        });
        return $this->success();
    }

    public function refund()
    {
        $id = $this->verifyRequiredId('id');
        OrderService::getInstance()->userRefund($this->userId(), $id);
        return $this->success();
    }
}
