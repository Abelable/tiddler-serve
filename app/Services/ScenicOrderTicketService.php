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
        $price,
        $number,
        ScenicTicket $ticketInfo
    )
    {
        $ticket = ScenicOrderTicket::new();
        $ticket->order_id = $orderId;
        $ticket->category_id = $categoryInfo->id;
        $ticket->category_name = $categoryInfo->name;
        $ticket->ticket_id = $ticketInfo->id;
        $ticket->name = $ticketInfo->name;
        $ticket->selected_date_timestamp = $timeStamp;
        $ticket->price = $price;
        $ticket->number = $number;
        $ticket->effective_time = $ticketInfo->effective_time;
        $ticket->validity_time = $ticketInfo->validity_time;
        $ticket->refund_status = $ticketInfo->refund_status;
        $ticket->need_exchange = $ticketInfo->need_exchange;
        $ticket->save();
    }

    public function getTicketByOrderId($orderId, $columns = ['*'])
    {
        return ScenicOrderTicket::query()->where('order_id', $orderId)->get($columns);
    }

    public function delete($orderId)
    {
        return ScenicOrderTicket::query()->where('order_id', $orderId)->delete();
    }
}
