<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\HotelOrder;
use App\Models\HotelOrderRoom;
use App\Services\AccountService;
use App\Services\CommissionService;
use App\Services\HotelManagerService;
use App\Services\HotelOrderRoomService;
use App\Services\HotelOrderService;
use App\Services\HotelOrderVerifyService;
use App\Services\HotelRoomService;
use App\Services\HotelRoomTypeService;
use App\Services\HotelService;
use App\Services\HotelShopService;
use App\Services\PromoterService;
use App\Services\RelationService;
use App\Utils\CodeResponse;
use App\Utils\Enums\HotelOrderEnums;
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

        $userId = $this->userId();
        $userLevel = $this->user()->promoterInfo->level ?: 0;
        $superiorId = RelationService::getInstance()->getSuperiorId($userId);
        $superiorLevel = PromoterService::getInstance()->getPromoterLevel($superiorId);
        $upperSuperiorId = RelationService::getInstance()->getSuperiorId($superiorId);
        $upperSuperiorLevel = PromoterService::getInstance()->getPromoterLevel($upperSuperiorId);

        $room = HotelRoomService::getInstance()->getRoomById($input->roomId);
        $shop = HotelShopService::getInstance()->getShopById($room->shop_id);

        $datePriceList = HotelOrderService::getInstance()->getDatePriceList($input->roomId, $input->checkInDate, $input->checkOutDate);
        $paymentAmount = (float)bcmul($datePriceList->pluck('price')->sum(), $input->num, 2);
        $averagePrice = round($datePriceList->avg('price'), 2);

        $orderId = DB::transaction(function () use ($upperSuperiorLevel, $upperSuperiorId, $superiorLevel, $superiorId, $userLevel, $userId, $paymentAmount, $shop, $room, $averagePrice, $input) {
            $order = HotelOrderService::getInstance()->createOrder($this->userId(), $input, $shop, $paymentAmount);

            // 生成核销码
            HotelOrderVerifyService::getInstance()->createVerifyCode($order->id, $room->hotel_id);

            // 生成订单房间快照
            $type = HotelRoomTypeService::getInstance()->getTypeById($room->type_id, ['*'], false);
            $hotel = HotelService::getInstance()->getHotelById($room->hotel_id);
            HotelOrderRoomService::getInstance()->createOrderRoom(
                $order->id,
                $hotel,
                $type,
                $room,
                $input,
                $averagePrice
            );

            // 生成佣金记录
            CommissionService::getInstance()
                ->createHotelCommission($order->id, $order->order_sn, $room, $paymentAmount, $userId, $userLevel, $superiorId, $superiorLevel, $upperSuperiorId, $upperSuperiorLevel);

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
        $order = HotelOrderService::getInstance()->createWxPayOrder($this->userId(), $orderId, $this->user()->openid);
        $payParams = Pay::wechat()->miniapp($order);
        return $this->success($payParams);
    }

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $status = $this->verifyRequiredInteger('status');

        $statusList = $this->statusList($status);
        $page = HotelOrderService::getInstance()->getOrderListByStatus($this->userId(), $statusList, $input);
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
        $page = HotelOrderService::getInstance()->getShopOrderList($shopId, $statusList, $input);
        $list = $this->orderList($page);

        return $this->success($this->paginate($page, $list));
    }

    private function statusList($status)
    {
        switch ($status) {
            case 1:
                $statusList = [HotelOrderEnums::STATUS_CREATE];
                break;
            case 2:
                $statusList = [HotelOrderEnums::STATUS_PAY];
                break;
            case 3:
                $statusList = [HotelOrderEnums::STATUS_SETTLE_IN];
                break;
            case 4:
                $statusList = [HotelOrderEnums::STATUS_CONFIRM, HotelOrderEnums::STATUS_AUTO_CONFIRM];
                break;
            case 5:
                $statusList = [HotelOrderEnums::STATUS_REFUND, HotelOrderEnums::STATUS_SUPPLIER_REFUND, HotelOrderEnums::STATUS_REFUND_CONFIRM];
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
        $roomList = HotelOrderRoomService::getInstance()->getListByOrderIds($orderIds)->keyBy('order_id');
        return $orderList->map(function (HotelOrder $order) use ($roomList) {
            /** @var HotelOrderRoom $room */
            $room = $roomList->get($order->id);
            $room->image_list = json_decode($room->image_list);
            $room->facility_list = json_decode($room->facility_list);

            return [
                'id' => $order->id,
                'status' => $order->status,
                'statusDesc' => HotelOrderEnums::STATUS_TEXT_MAP[$order->status],
                'shopId' => $order->shop_id,
                'shopLogo' => $order->shop_logo,
                'shopName' => $order->shop_name,
                'roomInfo' => $room,
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
        HotelOrderService::getInstance()->userCancel($this->userId(), $id);
        return $this->success();
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

    public function verify()
    {
        $code = $this->verifyRequiredString('code');

        $verifyCodeInfo = HotelOrderVerifyService::getInstance()->getByCode($code);
        if (is_null($verifyCodeInfo)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '无效核销码');
        }

        $order = HotelOrderService::getInstance()->getPendingSettleInOrderById($verifyCodeInfo->order_id);
        if (is_null($order)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '订单不存在');
        }

        $managerIds = HotelManagerService::getInstance()
            ->getManagerList($verifyCodeInfo->hotel_id)->pluck('user_id')->toArray();
        if (!in_array($this->userId(), $managerIds)) {
            return $this->fail(CodeResponse::PARAM_VALUE_ILLEGAL, '非当前酒店核销员，无法核销');
        }

        DB::transaction(function () use ($verifyCodeInfo, $order) {
            HotelOrderVerifyService::getInstance()->verify($verifyCodeInfo, $this->userId());
            HotelOrderService::getInstance()->userConfirm($order->user_id, $order->id);
        });

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        DB::transaction(function () use ($id) {
            HotelOrderService::getInstance()->delete($this->userId(), $id);
        });
        return $this->success();
    }

    public function refund()
    {
        $id = $this->verifyRequiredId('id');
        HotelOrderService::getInstance()->userRefund($this->userId(), $id);
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
}
