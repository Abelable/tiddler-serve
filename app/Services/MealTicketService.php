<?php

namespace App\Services;

use App\Models\MealTicket;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\MealTicketListInput;
use App\Utils\Inputs\MealTicketInput;
use App\Utils\Inputs\StatusPageInput;

class MealTicketService extends BaseService
{
    public function getList(MealTicketListInput $input, $columns=['*'])
    {
        $query = MealTicket::query()->whereIn('status', [0, 1, 2]);
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->restaurantId)) {
            $query = $query->whereIn('id', function ($subQuery) use ($input) {
                $subQuery->select('ticket_id')
                    ->from('restaurant_ticket')
                    ->where('restaurant_id', $input->restaurantId);
            });
        }
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getListByIds(array $ids, $columns=['*'])
    {
        return MealTicket::query()->where('status', 1)->whereIn('id', $ids)->get($columns);
    }

    public function getListTotal($userId, $status)
    {
        return MealTicket::query()->where('user_id', $userId)->where('status', $status)->count();
    }

    public function getTicketListByStatus($userId, StatusPageInput $input, $columns=['*'])
    {
        return MealTicket::query()
            ->where('user_id', $userId)
            ->where('status', $input->status)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getTicketById($id, $columns=['*'])
    {
        return MealTicket::query()->find($id, $columns);
    }

    public function getUserTicket($userId, $id, $columns=['*'])
    {
        return MealTicket::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function createTicket($userId, $providerId, MealTicketInput $input)
    {
        $ticket = MealTicket::new();
        $ticket->user_id = $userId;
        $ticket->provider_id = $providerId;

        return $this->updateTicket($ticket, $input);
    }

    public function updateTicket(MealTicket $ticket, MealTicketInput $input)
    {
        if ($ticket->status == 2) {
            $ticket->status = 0;
            $ticket->failure_reason = '';
        }
        $ticket->price = $input->price;
        $ticket->original_price = $input->originalPrice;
        $ticket->sales_commission_rate = $input->salesCommissionRate ?: 0;
        $ticket->promotion_commission_rate = $input->promotionCommissionRate ?: 0;
        $ticket->promotion_commission_upper_limit = $input->promotionCommissionUpperLimit ?: 0;
        $ticket->superior_promotion_commission_rate = $input->superiorPromotionCommissionRate ?: 0;
        $ticket->superior_promotion_commission_upper_limit = $input->superiorPromotionCommissionUpperLimit ?: 0;
        $ticket->validity_days = $input->validityDays ?: 0;
        $ticket->validity_start_time = $input->validityStartTime ?: '';
        $ticket->validity_end_time = $input->validityEndTime ?: '';
        $ticket->buy_limit = $input->buyLimit ?: 0;
        $ticket->per_table_usage_limit = $input->perTableUsageLimit ?: 0;
        $ticket->overlay_usage_limit = $input->overlayUsageLimit ?: 0;
        $ticket->use_time_list = json_encode($input->useTimeList);
        $ticket->inapplicable_products = json_encode($input->inapplicableProducts);
        $ticket->box_available = $input->boxAvailable;
        $ticket->need_pre_book = $input->needPreBook;
        $ticket->use_rules = json_encode($input->useRules);
        $ticket->save();

        return $ticket;
    }

    public function deleteTicket($userId, $id)
    {
        $ticket = $this->getUserTicket($userId, $id);
        if (is_null($ticket)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '代金券不存在');
        }
        $ticket->delete();
    }
}
