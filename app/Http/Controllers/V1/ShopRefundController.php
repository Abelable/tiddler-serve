<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Mall\Goods\Refund;
use App\Services\Mall\Goods\OrderGoodsService;
use App\Services\Mall\Goods\OrderService;
use App\Services\Mall\Goods\RefundService;
use App\Services\Mall\Goods\ShopRefundAddressService;
use App\Services\Mall\Goods\ShopService;
use App\Services\Mall\ShopTodoService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Enums\TodoEnums;
use App\Utils\ExpressServe;
use App\Utils\Inputs\RefundPageInput;
use Illuminate\Support\Facades\DB;

class ShopRefundController extends Controller
{
    public function waitingRefundCount()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $count = RefundService::getInstance()->getShopTotal($shopId, [0, 2]);
        return $this->success($count);
    }

    public function total()
    {
        $shopId = $this->verifyRequiredId('shopId');
        return $this->success([
            RefundService::getInstance()->getShopTotal($shopId, [0]),
            RefundService::getInstance()->getShopTotal($shopId, [2]),
        ]);
    }

    public function list()
    {
        /** @var RefundPageInput $input */
        $input = RefundPageInput::new();
        $shopId = $this->verifyRequiredId('shopId');
        $columns = [
            'id',
            'user_id',
            'status',
            'failure_reason',
            'order_sn',
            'order_goods_id',
            'refund_address_id',
            'refund_type',
            'refund_amount',
            'ship_channel',
            'ship_code',
            'ship_sn',
            'created_at',
            'updated_at'
        ];

        $page = RefundService::getInstance()->getShopRefundList($shopId, $input, $columns);
        $refundList = collect($page->items());

        $userIds = $refundList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()
            ->getListByIds($userIds, ['id', 'avatar', 'nickname'])
            ->keyBy('id');

        $orderGoodsIds = $refundList->pluck('order_goods_id')->toArray();
        $orderGoodsList = OrderGoodsService::getInstance()->getListByIds($orderGoodsIds)->keyBy('id');

        $refundAddressIds = $refundList->pluck('refund_address_id')->toArray();
        $refundAddressList = ShopRefundAddressService::getInstance()->getListByIds($refundAddressIds)->keyBy('id');

        $list = $refundList->map(function (Refund $refund) use ($refundAddressList, $userList, $orderGoodsList) {
            $userInfo = $userList->get($refund->user_id);
            $refund['userInfo'] = $userInfo;
            unset($refund['user_id']);

            $goodsInfo = $orderGoodsList->get($refund->order_goods_id);
            $refund['goodsInfo'] = $goodsInfo;
            unset($refund['order_goods_id']);

            $refundAddressInfo = $refundAddressList->get($refund->refund_address_id);
            $refund['refundAddressInfo'] = $refundAddressInfo;
            unset($refund['refund_address_id']);

            return $refund;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $refund = RefundService::getInstance()->getShopRefund($shopId, $id);
        if (is_null($refund)) {
            return $this->fail(CodeResponse::NOT_FOUND, '售后信息不存在');
        }

        $refund->image_list = json_decode($refund->image_list);

        $userInfo = UserService::getInstance()->getUserById($refund->user_id);
        $refund['userInfo'] = $userInfo;
        unset($refund['user_id']);

        $goods = OrderGoodsService::getInstance()->getOrderGoods($refund->order_id, $refund->goods_id);
        $refund['goodsInfo'] = $goods;
        unset($refund['order_goods_id']);
        unset($refund['goods_id']);

        $refundAddressInfo = ShopRefundAddressService::getInstance()->getAddressById($refund->refund_address_id);
        $refund['refundAddressInfo'] = $refundAddressInfo;
        unset($refund['refund_address_id']);

        return $this->success($refund);
    }

    public function approved()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $refund = RefundService::getInstance()->getShopRefund($shopId, $id);
        if (is_null($refund)) {
            return $this->fail(CodeResponse::NOT_FOUND, '售后信息不存在');
        }

        DB::transaction(function () use ($shopId, $refund) {
            if (($refund->status == 0 && $refund->refund_type == 1) || ($refund->status == 2 && $refund->refund_type == 2)) {
                $refund->status = 3;
                $refund->save();

                OrderService::getInstance()->afterSaleRefund(
                    $refund->order_id,
                    $refund->goods_id,
                    $refund->coupon_id,
                    $refund->refund_amount
                );
            } else {
                $refund->status = 1;
                $refund->save();
            }

            ShopTodoService::getInstance()->finishTodo($shopId, TodoEnums::REFUND_NOTICE, $refund->id);
            // todo 消息提醒 - 完成后台售后确认代办任务
//            NotificationService::getInstance()->addNotification(
//                NotificationEnums::REFUND_NOTICE,
//                '订单售后提醒', '您申请的售后订单已完成退款，请确认',
//                $refund->user_id,
//                $refund->order_id
//            );
        });

        return $this->success();
    }

    public function reject()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $refund = RefundService::getInstance()->getShopRefund($shopId, $id);
        if (is_null($refund)) {
            return $this->fail(CodeResponse::NOT_FOUND, '售后信息不存在');
        }

        DB::transaction(function () use ($shopId, $refund, $reason) {
            $refund->status = 4;
            $refund->failure_reason = $reason;
            $refund->save();

            ShopTodoService::getInstance()->finishTodo($shopId, TodoEnums::REFUND_NOTICE, $refund->id);
            // todo 消息提醒 - 完成后台售后确认代办任务
//            NotificationService::getInstance()->addNotification(
//                NotificationEnums::REFUND_NOTICE,
//                '订单售后驳回',
//                $reason,
//                $refund->user_id,
//                $refund->order_id
//            );
        });

        return $this->success();
    }

    public function shippingInfo()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $refund = RefundService::getInstance()->getShopRefund($shopId, $id);
        if (is_null($refund)) {
            return $this->fail(CodeResponse::NOT_FOUND, '售后信息不存在');
        }

        $shopInfo = ShopService::getInstance()->getShopById($shopId);
        $traces = ExpressServe::new()->track($refund->ship_code, $refund->ship_sn, $shopInfo->mobile);

        return $this->success([
            'shipCode' => $refund->ship_code,
            'shipSn' => $refund->ship_sn,
            'traces' => $traces
        ]);
    }
}
