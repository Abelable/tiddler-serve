<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Services\ShopTodoService;
use App\Services\NotificationService;
use App\Services\OrderGoodsService;
use App\Services\OrderService;
use App\Services\RefundService;
use App\Services\ShopService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Enums\NotificationEnums;
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
        $columns = ['id', 'user_id', 'status', 'failure_reason', 'order_sn', 'order_goods_id', 'refund_type', 'refund_amount', 'created_at', 'updated_at'];

        $page = RefundService::getInstance()->getShopRefundList($shopId, $input, $columns);
        $refundList = collect($page->items());

        $userIds = $refundList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()
            ->getListByIds($userIds, ['id', 'avatar', 'nickname'])
            ->keyBy('id');

        $orderGoodsIds = $refundList->pluck('order_goods_id')->toArray();
        $orderGoodsList = OrderGoodsService::getInstance()->getListByIds($orderGoodsIds)->keyBy('id');

        $list = $refundList->map(function (Refund $refund) use ($userList, $orderGoodsList) {
            $userInfo = $userList->get($refund->user_id);
            $refund['userInfo'] = $userInfo;
            unset($refund['user_id']);

            $goodsInfo = $orderGoodsList->get($refund->order_goods_id);
            $refund['goodsInfo'] = $goodsInfo;

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

        $goods = OrderGoodsService::getInstance()->getOrderGoods($refund->order_id, $refund->goods_id);
        $refund['goodsInfo'] = $goods;

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
            if (($refund->status == 0 && $refund->refund_type == 1)
                || ($refund->status == 2 && $refund->refund_type == 2)) {
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

            // 完成后台售后确认代办任务
            ShopTodoService::getInstance()->finishTodo($shopId, NotificationEnums::REFUND_NOTICE, $refund->id);
            NotificationService::getInstance()
                ->addNotification(NotificationEnums::REFUND_NOTICE, '订单售后提醒', '您申请的售后订单已完成退款，请确认', $refund->user_id, $refund->order_id);
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

        DB::transaction(function () use ($refund, $reason) {
            $refund->status = 4;
            $refund->failure_reason = $reason;
            $refund->save();

            // 完成后台售后确认代办任务
            ShopTodoService::getInstance()->finishTodo(NotificationEnums::REFUND_NOTICE, $refund->id);
            NotificationService::getInstance()
                ->addNotification(NotificationEnums::REFUND_NOTICE, '订单售后驳回', $reason, $refund->user_id, $refund->order_id);
        });

        return $this->success();
    }

    public function delete()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $refund = RefundService::getInstance()->getShopRefund($shopId, $id);
        if (is_null($refund)) {
            return $this->fail(CodeResponse::NOT_FOUND, '售后信息不存在');
        }

        $refund->delete();

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
