<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ScenicShop;
use App\Models\ScenicTicket;
use App\Services\ScenicShopManagerService;
use App\Services\ScenicShopService;
use App\Services\ScenicTicketCategoryService;
use App\Services\ScenicTicketService;
use App\Services\TicketScenicService;
use App\Services\TicketSpecService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ScenicTicketInput;
use App\Utils\Inputs\StatusPageInput;
use Illuminate\Support\Facades\DB;

class ScenicTicketController extends Controller
{
    protected $except = ['categoryOptions', 'listOfScenic'];

    public function categoryOptions()
    {
        $options = ScenicTicketCategoryService::getInstance()->getCategoryOptions(['id', 'name']);
        return $this->success($options);
    }

    public function listOfScenic()
    {
        $scenicId = $this->verifyRequiredId('scenicId');

        $ticketIds = TicketScenicService::getInstance()->getListByScenicId($scenicId)->pluck('ticket_id')->toArray();
        $ticketList = ScenicTicketService::getInstance()->getListByIds($ticketIds);

        $shopIds = $ticketList->pluck('shop_id')->toArray();
        $shopList = ScenicShopService::getInstance()->getShopListByIds($shopIds, ['id', 'name', 'type'])->keyBy('id');

        $ticketList = $ticketList->map(function (ScenicTicket $ticket) use ($shopList) {
            /** @var ScenicShop $shop */
            $shop = $shopList->get($ticket->shop_id);
            $ticket['shopInfo'] = $shop;

            unset($ticket->user_id);
            unset($ticket->shop_id);
            unset($ticket->provider_id);
            unset($ticket->status);
            unset($ticket->failure_reason);
            unset($ticket->promotion_commission_rate);
            unset($ticket->sales_commission_rate);
            unset($ticket->created_at);
            unset($ticket->updated_at);

            return $ticket;
        });

        return $this->success($ticketList);
    }

    public function ticketListTotals()
    {
        $shopId = $this->verifyRequiredId('shopId');

        return $this->success([
            ScenicTicketService::getInstance()->getListTotal($shopId, 1),
            ScenicTicketService::getInstance()->getListTotal($shopId, 3),
            ScenicTicketService::getInstance()->getListTotal($shopId, 0),
            ScenicTicketService::getInstance()->getListTotal($shopId, 2),
        ]);
    }

    public function shopTicketList()
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

    public function detail()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getShopTicket($shopId, $id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }

        $scenicIds = TicketScenicService::getInstance()->getListByTicketId($ticket->id)->pluck('scenic_id')->toArray();
        $specList = TicketSpecService::getInstance()->getSpecListByTicketId($ticket->id, ['category_id', 'price_list']);
        $ticket['scenicIds'] = $scenicIds;
        $ticket['specList'] = $specList;

        return $this->success($ticket);
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
