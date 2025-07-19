<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\MealTicket;
use App\Services\MealTicketService;
use App\Services\MealTicketRestaurantService;
use App\Utils\CodeResponse;

class MealTicketController extends Controller
{
    protected $only = [];

    public function list()
    {
        $restaurantId = $this->verifyRequiredId('restaurantId');

        $ticketIds = MealTicketRestaurantService::getInstance()->getListByRestaurantId($restaurantId)->pluck('ticket_id')->toArray();
        $ticketList = MealTicketService::getInstance()->getListByIds($ticketIds, [
            'price',
            'original_price',
            'sales_volume',
            'validity_days',
            'validity_start_time',
            'validity_end_time',
            'buy_limit',
            'per_table_usage_limit',
            'overlay_usage_limit',
            'use_time_list',
            'inapplicable_products',
            'box_available',
            'need_pre_book',
            'use_rules'
        ]);

        $ticketList = $ticketList->map(function (MealTicket $ticket) {
            $ticket->use_time_list = json_decode($ticket->use_time_list) ?: [];
            $ticket->inapplicable_products = json_decode($ticket->inapplicable_products) ?: [];
            $ticket->use_rules = json_decode($ticket->use_rules) ?: [];
            return $ticket;
        });

        return $this->success($ticketList);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = MealTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐券不存在');
        }

        $ticket['restaurantIds'] = $ticket->restaurantIds();
        $ticket->use_time_list = json_decode($ticket->use_time_list) ?: [];
        $ticket->inapplicable_products = json_decode($ticket->inapplicable_products) ?: [];
        $ticket->use_rules = json_decode($ticket->use_rules) ?: [];

        return $this->success($ticket);
    }
}
