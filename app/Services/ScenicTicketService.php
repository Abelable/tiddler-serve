<?php

namespace App\Services;

use App\Models\ScenicTicket;
use App\Utils\Inputs\Admin\ScenicTicketListInput;
use App\Utils\Inputs\ScenicTicketInput;
use App\Utils\Inputs\StatusPageInput;

class ScenicTicketService extends BaseService
{
    public function getList(ScenicTicketListInput $input, $columns=['*'])
    {
        $query = ScenicTicket::query()->whereIn('status', [0, 1, 2]);
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->type)) {
            $query = $query->where('type', $input->type);
        }
        if (!empty($input->scenicId)) {
            $query = $query->whereIn('id', function ($subQuery) use ($input) {
                $subQuery->select('ticket_id')
                    ->from('ticket_scenic_spot')
                    ->where('scenic_spot_id', $input->scenicId);
            });
        }
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getListByIds(array $ids, $columns=['*'])
    {
        return ScenicTicket::query()->where('status', 1)->whereIn('id', $ids)->with('specList')->get($columns);
    }

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
        if ($ticket->status == 2) {
            $ticket->status = 0;
            $ticket->failure_reason = '';
        }
        $ticket->type = $input->type;
        $ticket->name = $input->name;
        $ticket->brief_name = $input->briefName;
        $ticket->price = $input->price;
        $ticket->market_price = $input->marketPrice ?: '';
        $ticket->sales_commission_rate = $input->salesCommissionRate ?: 0;
        $ticket->promotion_commission_rate = $input->promotionCommissionRate ?: 0;
        $ticket->promotion_commission_upper_limit = $input->promotionCommissionUpperLimit ?: 0;
        $ticket->fee_include_tips = $input->feeIncludeTips ?: '';
        $ticket->fee_not_include_tips = $input->feeNotIncludeTips ?: '';
        $ticket->booking_time = $input->bookingTime;
        $ticket->effective_time = $input->effectiveTime ?: 0;
        $ticket->validity_time = $input->validityTime ?: 0;
        $ticket->limit_number = $input->limitNumber ?: 0;
        $ticket->refund_status = $input->refundStatus;
        $ticket->refund_tips = $input->refundTips ?: '';
        $ticket->need_exchange = $input->needExchange;
        $ticket->exchange_tips = $input->exchangeTips ?: '';
        $ticket->exchange_time = $input->exchangeTime ?: '';
        $ticket->exchange_location = $input->exchangeLocation ?: '';
        $ticket->enter_time = $input->enterTime ?: '';
        $ticket->enter_location = $input->enterLocation ?: '';
        $ticket->invoice_tips = $input->invoiceTips ?: '';
        $ticket->reminder_tips = $input->reminderTips ?: '';
        $ticket->save();

        return $ticket;
    }
}
