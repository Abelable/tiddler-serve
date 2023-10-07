<?php

namespace App\Services;

use App\Models\RestaurantTicket;

class RestaurantTicketService extends BaseService
{
    public function createRestaurantTickets($ticketId, array $restaurantIds)
    {
        foreach ($restaurantIds as $restaurantId) {
            $scenic = RestaurantTicket::new();
            $scenic->restaurant_id = $restaurantId;
            $scenic->ticket_id = $ticketId;
            $scenic->save();
        }
    }

    public function updateRestaurantTickets($ticketId, array $restaurantIds)
    {
        RestaurantTicket::query()->where('ticket_id', $ticketId)->delete();
        $this->createRestaurantTickets($ticketId, $restaurantIds);
    }

    public function getListByTicketId($ticketId, $columns = ['*'])
    {
        return RestaurantTicket::query()->where('ticket_id', $ticketId)->get($columns);
    }

    public function getListByRestaurantId($restaurantId, $columns = ['*'])
    {
        return RestaurantTicket::query()->where('restaurant_id', $restaurantId)->get($columns);
    }
}
