<?php

namespace App\Services;

use App\Models\OrderMealTicket;
use App\Models\MealTicket;

class OrderMealTicketService extends BaseService
{
    public function createOrderTicket(
        $orderId,
        $number,
        MealTicket $ticketInfo
    )
    {
        $ticket = OrderMealTicket::new();
        $ticket->order_id = $orderId;
        $ticket->ticket_id = $ticketInfo->id;
        $ticket->ticket_price = $ticketInfo->price;
        $ticket->number = $number;
        $ticket->save();
    }

    public function getTicketByOrderId($orderId, $columns = ['*'])
    {
        return OrderMealTicket::query()->where('order_id', $orderId)->first($columns);
    }

    public function getListByOrderIds(array $orderIds, $columns = ['*'])
    {
        return OrderMealTicket::query()->whereIn('order_id', $orderIds)->get($columns);
    }

    public function delete($orderId)
    {
        return OrderMealTicket::query()->where('order_id', $orderId)->delete();
    }
}
