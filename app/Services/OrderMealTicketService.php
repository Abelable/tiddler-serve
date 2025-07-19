<?php

namespace App\Services;

use App\Models\Catering\MealTicket;
use App\Models\Catering\OrderMealTicket;

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
        $ticket->number = $number;
        $ticket->ticket_id = $ticketInfo->id;
        $ticket->price = $ticketInfo->price;
        $ticket->original_price = $ticketInfo->original_price;
        $ticket->validity_days = $ticketInfo->validity_days;
        $ticket->validity_start_time = $ticketInfo->validity_start_time;
        $ticket->validity_end_time = $ticketInfo->validity_end_time;
        $ticket->buy_limit = $ticketInfo->buy_limit;
        $ticket->per_table_usage_limit = $ticketInfo->per_table_usage_limit;
        $ticket->overlay_usage_limit = $ticketInfo->overlay_usage_limit;
        $ticket->use_time_list = $ticketInfo->use_time_list;
        $ticket->inapplicable_products = $ticketInfo->inapplicable_products;
        $ticket->box_available = $ticketInfo->box_available;
        $ticket->need_pre_book = $ticketInfo->need_pre_book;
        $ticket->use_rules = $ticketInfo->use_rules;
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

    public function getListByOrderIdsAndMealTicketIds(array $orderIds, array $mealTicketIds,  $columns = ['*'])
    {
        return OrderMealTicket::query()
            ->whereIn('order_id', $orderIds)
            ->whereIn('meal_ticket_id', $mealTicketIds)
            ->get($columns);
    }

    public function delete($orderId)
    {
        return OrderMealTicket::query()->where('order_id', $orderId)->delete();
    }
}
