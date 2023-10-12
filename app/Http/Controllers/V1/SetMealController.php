<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\MealTicket;
use App\Services\MealTicketService;
use App\Services\RestaurantTicketService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\MealTicketInput;
use App\Utils\Inputs\StatusPageInput;
use Illuminate\Support\Facades\DB;

class SetMealController extends Controller
{
    protected $except = ['listByScenicId'];

    public function listByRestaurantId()
    {
        $restaurantId = $this->verifyRequiredId('restaurantId');

        $ticketIds = RestaurantTicketService::getInstance()->getListByRestaurantId($restaurantId)->pluck('ticket_id')->toArray();
        $ticketList = MealTicketService::getInstance()->getListByIds($ticketIds, [
            'name',
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

    public function listTotals()
    {
        return $this->success([
            MealTicketService::getInstance()->getListTotal($this->userId(), 1),
            MealTicketService::getInstance()->getListTotal($this->userId(), 3),
            MealTicketService::getInstance()->getListTotal($this->userId(), 0),
            MealTicketService::getInstance()->getListTotal($this->userId(), 2),
        ]);
    }

    public function userList()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();

        $page = MealTicketService::getInstance()->getTicketListByStatus($this->userId(), $input);
        $ticketList = collect($page->items());
        $list = $ticketList->map(function (MealTicket $ticket) {
            $ticket['restaurantIds'] = $ticket->restaurantIds();
            return $ticket;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = MealTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮代金券不存在');
        }

        $ticket['restaurantIds'] = $ticket->restaurantIds();
        $ticket->use_time_list = json_decode($ticket->use_time_list) ?: [];
        $ticket->inapplicable_products = json_decode($ticket->inapplicable_products) ?: [];
        $ticket->use_rules = json_decode($ticket->use_rules) ?: [];

        return $this->success($ticket);
    }

    public function add()
    {
        /** @var MealTicketInput $input */
        $input = MealTicketInput::new();

        $providerId = $this->user()->cateringProvider->id;
        if ($providerId == 0) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是服务商，无法上传餐饮代金券');
        }

        DB::transaction(function () use ($providerId, $input) {
            $ticket = MealTicketService::getInstance()->createTicket($this->userId(), $providerId, $input);
            RestaurantTicketService::getInstance()->createRestaurantTickets($ticket->id, $input->restaurantIds);
        });

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var MealTicketInput $input */
        $input = MealTicketInput::new();

        $ticket = MealTicketService::getInstance()->getUserTicket($this->userId(), $id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮代金券不存在');
        }

        DB::transaction(function () use ($input, $ticket) {
            MealTicketService::getInstance()->updateTicket($ticket, $input);
            RestaurantTicketService::getInstance()->updateRestaurantTickets($ticket->id, $input->restaurantIds);
        });

        return $this->success();
    }

    public function up()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = MealTicketService::getInstance()->getUserTicket($this->userId(), $id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮代金券不存在');
        }
        if ($ticket->status != 3) {
            return $this->fail(CodeResponse::FORBIDDEN, '非下架餐饮代金券，无法上架');
        }
        $ticket->status = 1;
        $ticket->save();

        return $this->success();
    }

    public function down()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = MealTicketService::getInstance()->getUserTicket($this->userId(), $id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮代金券不存在');
        }
        if ($ticket->status != 1) {
            return $this->fail(CodeResponse::FORBIDDEN, '非售卖中餐饮代金券，无法下架');
        }
        $ticket->status = 3;
        $ticket->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        DB::transaction(function () use ($id) {
            MealTicketService::getInstance()->deleteTicket($this->userId(), $id);
            RestaurantTicketService::getInstance()->deleteByTicketId($id);
        });

        return $this->success();
    }
}
