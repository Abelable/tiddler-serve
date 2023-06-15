<?php

namespace App\Services;

use App\Models\ScenicTicket;
use App\Models\TicketScenicSpot;
use App\Models\TicketSpec;
use App\Utils\Inputs\ScenicTicketInput;
use App\Utils\Inputs\StatusPageInput;

class ScenicTicketService extends BaseService
{
    public function getListTotal($userId, $status)
    {
        return ScenicTicket::query()->where('user_id', $userId)->where('status', $status)->count();
    }

    public function getTicketListByStatus($userId, StatusPageInput $input, $columns=['*'])
    {
        return ScenicTicket::query()
            ->where('user_id', $userId)
            ->where('status', $input->status)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getTicketById($id, $columns=['*'])
    {
        return ScenicTicket::query()->find($id, $columns);
    }

    public function getUserTicket($userId, $id, $columns=['*'])
    {
        return ScenicTicket::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function createTicket($userId, $providerId, $shopId, ScenicTicketInput $input)
    {
        $ticket = ScenicTicket::new();
        $ticket->user_id = $userId;
        $ticket->provider_id = $providerId;
        $ticket->shop_id = $shopId;

        return $this->updateTicket($ticket, $input);
    }

    public function updateTicket(ScenicTicket $ticket, ScenicTicketInput $input)
    {
        $ticket->type = $input->type;
        $ticket->name = $input->name;
        $ticket->price = $input->price;
        if (!empty($input->marketPrice)) {
            $ticket->market_price = $input->marketPrice;
        }
        $ticket->sales_commission_rate = $input->salesCommissionRate;
        $ticket->promotion_commission_rate = $input->promotionCommissionRate;
        if (!empty($input->feeIncludeTips)) {
            $ticket->fee_include_tips = $input->feeIncludeTips;
        }
        if (!empty($input->feeNotIncludeTips)) {
            $ticket->fee_not_include_tips = $input->feeNotIncludeTips;
        }
        if (!empty($input->bookingTime)) {
            $ticket->booking_time = $input->bookingTime;
        }
        if (!empty($input->effectiveTime)) {
            $ticket->effective_time = $input->effectiveTime;
        }
        if (!empty($input->validityTime)) {
            $ticket->validity_time = $input->validityTime;
        }
        if (!empty($input->limitNumber)) {
            $ticket->limit_number = $input->limitNumber;
        }
        $ticket->refund_status = $input->refundStatus;
        if (!empty($input->refundTips)) {
            $ticket->refund_tips = $input->refundTips;
        }
        $ticket->need_exchange = $input->needExchange;
        if (!empty($input->exchangeTips)) {
            $ticket->exchange_tips = $input->exchangeTips;
        }
        if (!empty($input->exchangeTime)) {
            $ticket->exchange_time = $input->exchangeTime;
        }
        if (!empty($input->exchangeLocation)) {
            $ticket->exchange_location = $input->exchangeLocation;
        }
        if (!empty($input->otherTips)) {
            $ticket->other_tips = $input->otherTips;
        }
        $ticket->save();

        return $ticket;
    }

    public function createTicketScenicSpots($ticketId, array $scenicIds)
    {
        foreach ($scenicIds as $scenicId) {
            $scenic = TicketScenicSpot::new();
            $scenic->ticket_id = $ticketId;
            $scenic->scenic_id = $scenicId;
            $scenic->save();
        }
    }

    public function updateTicketScenicSpots($ticketId, array $scenicIds)
    {
        TicketScenicSpot::query()->where('ticket_id', $ticketId)->delete();
        $this->createTicketScenicSpots($ticketId, $scenicIds);
    }

    public function createTicketSpecList($ticketId, array $specList)
    {
        foreach ($specList as $spec) {
            $ticketSpec = TicketSpec::new();
            $ticketSpec->ticket_id = $ticketId;
            $ticketSpec->category_id = $spec['categoryId'];
            $ticketSpec->price_list = $spec['priceList'];
            $ticketSpec->save();
        }
    }

    public function updateTicketSpecList($ticketId, array $specList)
    {
        TicketSpec::query()->where('ticket_id', $ticketId)->delete();
        $this->updateTicketSpecList($ticketId, $specList);
    }
}
