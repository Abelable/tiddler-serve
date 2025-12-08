<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Mall\Goods\Refund;
use App\Services\Mall\Goods\OrderService;
use App\Services\Mall\Goods\RefundService;
use App\Services\Mall\ShopTodoService;
use App\Services\SystemTodoService;
use App\Utils\CodeResponse;
use App\Utils\Enums\TodoEnums;
use App\Utils\Inputs\RefundInput;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    public function refundAmount()
    {
        $orderId = $this->verifyRequiredId('orderId');
        $goodsId = $this->verifyRequiredId('goodsId');
        $couponId = $this->verifyId('couponId');

        $refundAmount = OrderService::getInstance()->calcRefundAmount($orderId, $goodsId, $couponId);

        return $this->success($refundAmount);
    }

    public function detail()
    {
        $orderId = $this->verifyRequiredId('orderId');
        $goodsId = $this->verifyRequiredId('goodsId');
        $columns = ['id', 'status', 'failure_reason', 'refund_amount', 'refund_type', 'refund_reason', 'image_list', 'ship_channel', 'ship_code', 'ship_sn'];
        $refund = RefundService::getInstance()->getRefundByUserId($this->userId(), $orderId, $goodsId, $columns);
        if (!is_null($refund)) {
            $refund->image_list = json_decode($refund->image_list);
        }
        return $this->success($refund);
    }

    public function add()
    {
        /** @var RefundInput $input */
        $input = RefundInput::new();

        $refundAmount = OrderService::getInstance()->calcRefundAmount($input->orderId, $input->goodsId, $input->couponId);

        DB::transaction(function () use ($refundAmount, $input) {
            $refund = RefundService::getInstance()->createRefund($this->userId(), $input, $refundAmount);

            OrderService::getInstance()->afterSale($this->userId(), $input->orderId);

            // todo 售后通知
            if ($refund->shop_id != 0) {
                ShopTodoService::getInstance()->createTodo($refund->shop_id, TodoEnums::REFUND_NOTICE, [$refund->id]);
            } else {
                SystemTodoService::getInstance()->createTodo(TodoEnums::REFUND_NOTICE, [$refund->id]);
            }
        });

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        $type = $this->verifyRequiredInteger('type');
        $reason = $this->verifyRequiredString('reason');
        $imageList = $this->verifyArray('imageList');

        /** @var Refund $refund */
        $refund = RefundService::getInstance()->getRefundById($id);
        if (is_null($refund)) {
            return $this->fail(CodeResponse::NOT_FOUND, '退款信息不存在');
        }

        DB::transaction(function () use ($imageList, $reason, $type, $refund) {
            RefundService::getInstance()->updateRefund($refund, $type, $reason, $imageList);

            // todo 售后通知
            if ($refund->shop_id != 0) {
                ShopTodoService::getInstance()->createTodo($refund->shop_id, TodoEnums::REFUND_NOTICE, [$refund->id]);
            } else {
                SystemTodoService::getInstance()->createTodo(TodoEnums::REFUND_NOTICE, [$refund->id]);
            }
        });

        return $this->success();
    }

    public function submitShippingInfo()
    {
        $id = $this->verifyRequiredId('id');
        $shipChannel = $this->verifyRequiredString('shipChannel');
        $shipCode = $this->verifyRequiredString('shipCode');
        $shipSn = $this->verifyRequiredString('shipSn');

        $refund = RefundService::getInstance()->getUserRefund($this->userId(), $id);
        if (is_null($refund)) {
            return $this->fail(CodeResponse::NOT_FOUND, '退款信息不存在');
        }
        if ($refund->status != 1) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '后台未审核通过，无法上传物流信息');
        }

        $refund->status = 2;
        $refund->ship_channel = $shipChannel;
        $refund->ship_code = $shipCode;
        $refund->ship_sn = $shipSn;
        $refund->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $refund = RefundService::getInstance()->getUserRefund($this->userId(), $id);
        if (is_null($refund)) {
            return $this->fail(CodeResponse::NOT_FOUND, '退款信息不存在');
        }
        $refund->delete();
        return $this->success();
    }
}
