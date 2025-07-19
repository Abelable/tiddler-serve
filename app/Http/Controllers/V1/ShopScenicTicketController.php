<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ScenicTicket;
use App\Services\ScenicShopManagerService;
use App\Services\ScenicTicketService;
use App\Services\TicketScenicService;
use App\Services\TicketSpecService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ScenicTicketInput;
use App\Utils\Inputs\StatusPageInput;
use Illuminate\Support\Facades\DB;

class ShopScenicTicketController extends Controller
{
    public function totals()
    {
        $shopId = $this->verifyRequiredId('shopId');

        return $this->success([
            ScenicTicketService::getInstance()->getListTotal($shopId, 1),
            ScenicTicketService::getInstance()->getListTotal($shopId, 3),
            ScenicTicketService::getInstance()->getListTotal($shopId, 0),
            ScenicTicketService::getInstance()->getListTotal($shopId, 2),
        ]);
    }

    public function list()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();
        $shopId = $this->verifyRequiredId('shopId');

        $page = ScenicTicketService::getInstance()->getTicketListByStatus($shopId, $input);
        $ticketList = collect($page->items());
        $list = $ticketList->map(function (ScenicTicket $ticket) {
            $ticket['scenicIds'] = $ticket->scenicIds();
            return $ticket;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function add()
    {
        /** @var ScenicTicketInput $input */
        $input = ScenicTicketInput::new();
        $shopId = $this->verifyRequiredId('shopId');

        $shopManagerIds = ScenicShopManagerService::getInstance()
            ->getManagerList($shopId)->pluck('user_id')->toArray();
        if ($shopId != $this->user()->scenicShop->id && !in_array($this->userId(), $shopManagerIds)) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是当前店铺商家或管理员，无权限添加景点门票');
        }

        DB::transaction(function () use ($shopId, $input) {
            $ticket = ScenicTicketService::getInstance()->createTicket($shopId, $input);
            TicketScenicService::getInstance()->createTicketScenicSpots($ticket->id, $input->scenicIds);
            TicketSpecService::getInstance()->createTicketSpecList($ticket->id, $input->specList);
        });

        return $this->success();
    }

    public function edit()
    {
        /** @var ScenicTicketInput $input */
        $input = ScenicTicketInput::new();
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $shopManagerIds = ScenicShopManagerService::getInstance()
            ->getManagerList($shopId)->pluck('user_id')->toArray();
        if ($shopId != $this->user()->scenicShop->id && !in_array($this->userId(), $shopManagerIds)) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是当前店铺商家或管理员，无权限添加景点门票');
        }

        $ticket = ScenicTicketService::getInstance()->getShopTicket($shopId, $id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }

        DB::transaction(function () use ($input, $ticket) {
            ScenicTicketService::getInstance()->updateTicket($ticket, $input);
            TicketScenicService::getInstance()->updateTicketScenicSpots($ticket->id, $input->scenicIds);
            TicketSpecService::getInstance()->updateTicketSpecList($ticket->id, $input->specList);
        });

        return $this->success();
    }

    public function up()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getShopTicket($shopId, $id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }
        if ($ticket->status != 3) {
            return $this->fail(CodeResponse::FORBIDDEN, '非下架景点门票，无法上架');
        }
        $ticket->status = 1;
        $ticket->save();

        return $this->success();
    }

    public function down()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getShopTicket($shopId, $id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }
        if ($ticket->status != 1) {
            return $this->fail(CodeResponse::FORBIDDEN, '非售卖中景点门票，无法下架');
        }
        $ticket->status = 3;
        $ticket->save();

        return $this->success();
    }

    public function delete()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getShopTicket($shopId, $id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }
        $ticket->delete();

        return $this->success();
    }
}
