<?php

namespace App\Services\Mall\Catering;

use App\Models\Mall\Catering\MealTicket;
use App\Models\Mall\Catering\OrderMealTicket;
use App\Services\BaseService;

class OrderMealTicketService extends BaseService
{
    public function createOrderTicket(
        $userId,
        $orderId,
        $restaurantId,
        $restaurantCover,
        $restaurantName,
        $number,
        MealTicket $ticketInfo
    )
    {
        $ticket = OrderMealTicket::new();
        $ticket->user_id = $userId;
        $ticket->order_id = $orderId;
        $ticket->restaurant_id = $restaurantId;
        $ticket->restaurant_cover = $restaurantCover;
        $ticket->restaurant_name = $restaurantName;
        $ticket->ticket_id = $ticketInfo->id;
        $ticket->number = $number;
        $ticket->price = $ticketInfo->price;
        $ticket->original_price = $ticketInfo->original_price;
        $ticket->sales_commission_rate = $ticketInfo->sales_commission_rate;
        $ticket->promotion_commission_rate = $ticketInfo->promotion_commission_rate;
        $ticket->promotion_commission_upper_limit = $ticketInfo->promotion_commission_upper_limit;
        $ticket->superior_promotion_commission_rate = $ticketInfo->superior_promotion_commission_rate;
        $ticket->superior_promotion_commission_upper_limit = $ticketInfo->superior_promotion_commission_upper_limit;
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
            ->whereIn('ticket_id', $mealTicketIds)
            ->get($columns);
    }

    public function searchList($userId, $keyword, $columns = ['*'])
    {
        return OrderMealTicket::query()
            ->where('user_id', $userId)
            ->where('restaurant_name', 'like', "%{$keyword}%")
            ->get($columns);
    }

    public function delete($orderId)
    {
        return OrderMealTicket::query()->where('order_id', $orderId)->delete();
    }
}
