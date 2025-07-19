<?php

namespace App\Services;

use App\Models\Catering\MealTicketRestaurant;

class MealTicketRestaurantService extends BaseService
{
    public function create($ticketId, array $restaurantIds)
    {
        foreach ($restaurantIds as $restaurantId) {
            $scenic = MealTicketRestaurant::new();
            $scenic->meal_ticket_id = $ticketId;
            $scenic->restaurant_id = $restaurantId;
            $scenic->save();
        }
    }

    public function update($ticketId, array $restaurantIds)
    {
        $this->deleteByTicketId($ticketId);
        $this->create($ticketId, $restaurantIds);
    }

    public function getListByTicketId($ticketId, $columns = ['*'])
    {
        return MealTicketRestaurant::query()->where('meal_ticket_id', $ticketId)->get($columns);
    }

    public function getListByRestaurantId($restaurantId, $columns = ['*'])
    {
        return MealTicketRestaurant::query()->where('restaurant_id', $restaurantId)->get($columns);
    }

    public function deleteByTicketId($ticketId)
    {
        MealTicketRestaurant::query()->where('meal_ticket_id', $ticketId)->delete();
    }
}
