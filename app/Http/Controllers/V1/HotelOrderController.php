<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\HotelOrder;
use App\Models\HotelOrderRoom;
use App\Services\AccountService;
use App\Services\CommissionService;
use App\Services\HotelOrderRoomService;
use App\Services\HotelOrderService;
use App\Services\HotelOrderVerifyService;
use App\Services\HotelRoomService;
use App\Services\HotelRoomTypeService;
use App\Services\HotelService;
use App\Services\HotelShopIncomeService;
use App\Services\HotelShopService;
use App\Services\PromoterService;
use App\Services\RelationService;
use App\Utils\CodeResponse;
use App\Utils\Enums\AccountChangeType;
use App\Utils\Enums\HotelOrderStatus;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\HotelOrderInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yansongda\LaravelPay\Facades\Pay;

class HotelOrderController extends Controller
{
    public function paymentAmount()
    {
        $roomId = $this->verifyRequiredId('roomId');
        $checkInDate = $this->verifyRequiredInteger('checkInDate');
        $checkOutDate = $this->verifyRequiredInteger('checkOutDate');
        $num = $this->verifyRequiredInteger('num');
        $useBalance = $this->verifyBoolean('useBalance', false);

        $datePriceList = HotelOrderService::getInstance()->getDatePriceList($roomId, $checkInDate, $checkOutDate);
        $totalPrice = (float)bcmul($datePriceList->pluck('price')->sum(), $num, 2);

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
        /** @var HotelOrderInput $input */
        $input = HotelOrderInput::new();

        // 分布式锁，防止重复请求
        $lockKey = sprintf('create_hotel_order_%s_%s', $this->userId(), md5(serialize($input)));
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

        $room = HotelRoomService::getInstance()->getRoomById($input->roomId);
        $hotel = HotelService::getInstance()->getHotelById($room->hotel_id);
        $shop = HotelShopService::getInstance()->getShopById($room->shop_id);

        $datePriceList = HotelOrderService::getInstance()->getDatePriceList($input->roomId, $input->checkInDate, $input->checkOutDate);
        $averagePrice = round($datePriceList->avg('price'), 2);
        $totalPrice = (float)bcmul($datePriceList->pluck('price')->sum(), $input->num, 2);
        $paymentAmount = $totalPrice;

        // 余额抵扣
        $deductionBalance = 0;
        if ($input->useBalance == 1) {
            $account = AccountService::getInstance()->getUserAccount($userId);
            $deductionBalance = min($paymentAmount, $account->balance);
            $paymentAmount = bcsub($paymentAmount, $deductionBalance, 2);
        }

        $orderId = DB::transaction(function () use (
            $hotel,
            $totalPrice,
            $deductionBalance,
            $upperSuperiorLevel,
            $upperSuperiorId,
            $superiorLevel,
            $superiorId,
            $userLevel,
            $userId,
            $paymentAmount,
            $shop,
            $room,
            $averagePrice,
            $input
        ) {
            $order = HotelOrderService::getInstance()->createOrder(
                $userId,
                $input,
                $hotel,
                $shop,
                $totalPrice,
                $deductionBalance,
                $paymentAmount
            );

            // 生成订单房间快照
            $type = HotelRoomTypeService::getInstance()->getTypeById($room->type_id, ['*'], false);
            HotelOrderRoomService::getInstance()->createOrderRoom(
                $userId,
                $order->id,
                $hotel,
                $type,
                $room,
                $input,
                $averagePrice
            );

            // 生成核销码
            HotelOrderVerifyService::getInstance()->createVerifyCode($order->id, $room->hotel_id);

            // 生成佣金记录
            CommissionService::getInstance()->createHotelCommission(
                $order->id,
                $order->order_sn,
                $room,
                $paymentAmount,
                $userId,
                $userLevel,
                $superiorId,
                $superiorLevel,
                $upperSuperiorId,
                $upperSuperiorLevel
            );

            // 生成店铺收益
            HotelShopIncomeService::getInstance()
                ->createIncome($shop->id, $order->id, $order->order_sn, $room, $paymentAmount);

            if ($input->useBalance == 1) {
                // 更新余额
                AccountService::getInstance()->updateBalance(
                    $userId,
                    AccountChangeType::PURCHASE,
                    -$deductionBalance,
                    $order->order_sn,
                    ProductType::HOTEL
                );
            }

            // 增加酒店、房间销量
            HotelService::getInstance()->increaseSalesVolume($room->hotel_id, $input->num);
            $room->sales_volume = $room->sales_volume + $input->num;
            $room->save();

            return $order->id;
        });

        return $this->success($orderId);
    }

    public function payParams()
    {
        $orderId = $this->verifyRequiredInteger('orderId');
        $order = HotelOrderService::getInstance()
            ->createWxPayOrder($this->userId(), $orderId, $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($order);
        return $this->success($payParams);
    }

    public function total()
    {
        return $this->success([
            HotelOrderService::getInstance()->getTotal($this->userId(), $this->statusList(1)),
            HotelOrderService::getInstance()->getTotal($this->userId(), $this->statusList(2)),
            HotelOrderService::getInstance()->getTotal($this->userId(), $this->statusList(3)),
            HotelOrderService::getInstance()->getTotal($this->userId(), $this->statusList(4)),
            HotelOrderService::getInstance()->getTotal($this->userId(), [HotelOrderStatus::REFUNDING]),
        ]);
    }

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');

        $statusList = $this->statusList($status);
        $page = HotelOrderService::getInstance()->getOrderListByStatus($this->userId(), $statusList, $input);
        $orderList = collect($page->items());
        $list = $this->handleOrderList($orderList);

        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        $keywords = $this->verifyRequiredString('keywords');

        $orderGoodsList = HotelOrderRoomService::getInstance()->searchList($this->userId(), $keywords);
        $orderIds = $orderGoodsList->pluck('order_id')->toArray();
        $orderList = HotelOrderService::getInstance()->getOrderListByIds($orderIds);
        $list = $this->handleOrderList($orderList);

        return $this->success($list);
    }

    private function statusList($status)
    {
        switch ($status) {
            case 1:
                $statusList = [HotelOrderStatus::CREATED];
                break;
            case 2:
                $statusList = [HotelOrderStatus::PAID];
                break;
            case 3:
                $statusList = [HotelOrderStatus::MERCHANT_APPROVED];
                break;
            case 4:
                $statusList = [
                    HotelOrderStatus::CONFIRMED,
                    HotelOrderStatus::AUTO_CONFIRMED,
                    HotelOrderStatus::ADMIN_CONFIRMED
                ];
                break;
            case 5:
                $statusList = [
                    HotelOrderStatus::REFUNDING,
                    HotelOrderStatus::REFUNDED,
                    HotelOrderStatus::MERCHANT_REJECTED
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
        $roomList = HotelOrderRoomService::getInstance()->getListByOrderIds($orderIds)->keyBy('order_id');
        return $orderList->map(function (HotelOrder $order) use ($roomList) {
            /** @var HotelOrderRoom $room */
            $room = $roomList->get($order->id);
            $room->image_list = json_decode($room->image_list);
            $room->facility_list = json_decode($room->facility_list);

            return [
                'id' => $order->id,
                'orderSn' => $order->order_sn,
                'status' => $order->status,
                'statusDesc' => HotelOrderStatus::TEXT_MAP[$order->status],
                'shopId' => $order->shop_id,
                'shopLogo' => $order->shop_logo,
                'shopName' => $order->shop_name,
                'hotelId' => $order->hotel_id,
                'hotelCover' => $order->hotel_cover,
                'hotelName' => $order->hotel_name,
                'roomInfo' => $room,
                'totalPrice' => $order->total_price,
                'paymentAmount' => $order->payment_amount,
                'consignee' => $order->consignee,
                'mobile' => $order->mobile,
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
            'consignee',
            'mobile',
            'shop_id',
            'shop_logo',
            'shop_name',
            'hotel_id',
            'hotel_cover',
            'hotel_name',
            'total_price',
            'payment_amount',
            'pay_time',
            'approve_time',
            'confirm_time',
            'created_at',
            'updated_at',
        ];

        $order = HotelOrderService::getInstance()->getOrderById($this->userId(), $id, $columns);
        if (is_null($order)) {
            return $this->fail(CodeResponse::NOT_FOUND, '订单不存在');
        }

        $room = HotelOrderRoomService::getInstance()->getRoomByOrderId($order->id);
        $room->image_list = json_decode($room->image_list);
        $room->facility_list = json_decode($room->facility_list);
        $order['roomInfo'] = $room;

        return $this->success($order);
    }

    public function verifyCode()
    {
        $orderId = $this->verifyRequiredId('orderId');
        $hotelId = $this->verifyRequiredId('hotelId');

        $verifyCodeInfo = HotelOrderVerifyService::getInstance()->getVerifyCodeInfo($orderId, $hotelId);
        if (is_null($verifyCodeInfo)) {
            return $this->fail(CodeResponse::NOT_FOUND, '核销信息不存在');
        }

        return $this->success($verifyCodeInfo->code);
    }

    public function cancel()
    {
        $id = $this->verifyRequiredId('id');
        HotelOrderService::getInstance()->userCancel($this->userId(), $id);
        return $this->success();
    }

    public function refund()
    {
        $id = $this->verifyRequiredId('id');
        HotelOrderService::getInstance()->userRefund($this->userId(), $id);
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        DB::transaction(function () use ($id) {
            HotelOrderService::getInstance()->delete($this->userId(), $id);
            HotelOrderRoomService::getInstance()->delete($id);
        });

        return $this->success();
    }
}
