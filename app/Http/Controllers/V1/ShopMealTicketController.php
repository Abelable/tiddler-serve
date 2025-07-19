<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Catering\MealTicket;
use App\Services\Mall\Catering\CateringShopManagerService;
use App\Services\MealTicketRestaurantService;
use App\Services\MealTicketService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\MealTicketInput;
use App\Utils\Inputs\StatusPageInput;
use Illuminate\Support\Facades\DB;

class ShopMealTicketController extends Controller
{
    public function totals()
    {
        $shopId = $this->verifyRequiredId('shopId');

        return $this->success([
            MealTicketService::getInstance()->getListTotal($shopId, 1),
            MealTicketService::getInstance()->getListTotal($shopId, 3),
            MealTicketService::getInstance()->getListTotal($shopId, 0),
            MealTicketService::getInstance()->getListTotal($shopId, 2),
        ]);
    }

    public function list()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();
        $shopId = $this->verifyRequiredId('shopId');

        $page = MealTicketService::getInstance()->getTicketListByStatus($shopId, $input);
        $ticketList = collect($page->items());
        $list = $ticketList->map(function (MealTicket $ticket) {
            $ticket['restaurantIds'] = $ticket->restaurantIds();
            return $ticket;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function add()
    {
        /** @var MealTicketInput $input */
        $input = MealTicketInput::new();
        $shopId = $this->verifyRequiredId('shopId');

        $shopManagerIds = CateringShopManagerService::getInstance()
            ->getManagerList($shopId)->pluck('user_id')->toArray();
        if ($shopId != $this->user()->cateringShop->id && !in_array($this->userId(), $shopManagerIds)) {
            return $this
                ->fail(CodeResponse::FORBIDDEN, '您不是当前餐饮门店商家或管理员，无权限添加餐券');
        }

        DB::transaction(function () use ($shopId, $input) {
            $ticket = MealTicketService::getInstance()->createTicket($shopId, $input);
            MealTicketRestaurantService::getInstance()->create($ticket->id, $input->restaurantIds);
        });

        return $this->success();
    }

    public function edit()
    {
        /** @var MealTicketInput $input */
        $input = MealTicketInput::new();
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $ticket = MealTicketService::getInstance()->getShopTicket($shopId, $id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐券不存在');
        }

        DB::transaction(function () use ($input, $ticket) {
            MealTicketService::getInstance()->updateTicket($ticket, $input);
            MealTicketRestaurantService::getInstance()->update($ticket->id, $input->restaurantIds);
        });

        return $this->success();
    }

    public function up()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $ticket = MealTicketService::getInstance()->getShopTicket($shopId, $id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐券不存在');
        }

        if ($ticket->status != 3) {
            return $this->fail(CodeResponse::FORBIDDEN, '非下架餐券，无法上架');
        }
        $ticket->status = 1;
        $ticket->save();

        return $this->success();
    }

    public function down()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $ticket = MealTicketService::getInstance()->getShopTicket($shopId, $id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐券不存在');
        }

        if ($ticket->status != 1) {
            return $this->fail(CodeResponse::FORBIDDEN, '非售卖中餐券，无法下架');
        }
        $ticket->status = 3;
        $ticket->save();

        return $this->success();
    }

    public function delete()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $ticket = MealTicketService::getInstance()->getShopTicket($shopId, $id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐券不存在');
        }

        DB::transaction(function () use ($shopId, $id) {
            MealTicketService::getInstance()->deleteTicket($shopId, $id);
            MealTicketRestaurantService::getInstance()->deleteByTicketId($id);
        });

        return $this->success();
    }
}
