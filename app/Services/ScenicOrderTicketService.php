<?php

namespace App\Services;

use App\Models\ScenicOrderTicket;
use App\Models\ScenicTicket;
use App\Models\ScenicTicketCategory;

class ScenicOrderTicketService extends BaseService
{
    public function createOrderTicket(
        $orderId,
        ScenicTicketCategory $categoryInfo,
        $timeStamp,
        $priceUnit,
        $number,
        ScenicTicket $ticket
    )
    {
        $orderTicket = ScenicOrderTicket::new();
        $orderTicket->order_id = $orderId;
        $orderTicket->category_id = $categoryInfo->id;
        $orderTicket->category_name = $categoryInfo->name;
        $orderTicket->ticket_id = $ticket->id;
        $orderTicket->name = $ticket->name;
        $orderTicket->selected_date_timestamp = $timeStamp;
        $orderTicket->price = $priceUnit->price;
        $orderTicket->sales_commission_rate = $priceUnit->salesCommissionRate ?? $ticket->sales_commission_rate;
        $orderTicket->promotion_commission_rate = $ticket->promotion_commission_rate;
        $orderTicket->promotion_commission_upper_limit = $ticket->promotion_commission_upper_limit;
        $orderTicket->superior_promotion_commission_rate = $ticket->superior_promotion_commission_rate;
        $orderTicket->superior_promotion_commission_upper_limit = $ticket->superior_promotion_commission_upper_limit;
        $orderTicket->number = $number;
        $orderTicket->effective_time = $ticket->effective_time;
        $orderTicket->validity_time = $ticket->validity_time;
        $orderTicket->refund_status = $ticket->refund_status;
        $orderTicket->need_exchange = $ticket->need_exchange;
        $orderTicket->save();
    }

    public function getTicketByOrderId($orderId, $columns = ['*'])
    {
        return ScenicOrderTicket::query()->where('order_id', $orderId)->first($columns);
    }

    public function getListByOrderIds(array $orderIds, $columns = ['*'])
    {
        return ScenicOrderTicket::query()->whereIn('order_id', $orderIds)->get($columns);
    }

    public function delete($orderId)
    {
        return ScenicOrderTicket::query()->where('order_id', $orderId)->delete();
    }

    public function searchList($userId, $keyword, $columns = ['*'])
    {
        // todo 景点订单搜索
        return ScenicOrderTicket::query()
            ->where('user_id', $userId)
            ->where('name', 'like', "%{$keyword}%")
            ->get($columns);
    }
}
