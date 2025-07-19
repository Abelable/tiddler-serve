<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ScenicShop;
use App\Models\ScenicTicket;
use App\Services\ScenicShopService;
use App\Services\ScenicTicketCategoryService;
use App\Services\ScenicTicketService;
use App\Services\TicketScenicService;
use App\Services\TicketSpecService;
use App\Utils\CodeResponse;

class ScenicTicketController extends Controller
{
    protected $only = [];

    public function categoryOptions()
    {
        $options = ScenicTicketCategoryService::getInstance()->getCategoryOptions(['id', 'name']);
        return $this->success($options);
    }

    public function list()
    {
        $scenicId = $this->verifyRequiredId('scenicId');

        $ticketIds = TicketScenicService::getInstance()
            ->getListByScenicId($scenicId)->pluck('ticket_id')->toArray();
        $ticketList = ScenicTicketService::getInstance()->getListByIds($ticketIds);

        $shopIds = $ticketList->pluck('shop_id')->toArray();
        $shopList = ScenicShopService::getInstance()
            ->getShopListByIds($shopIds, ['id', 'name', 'type'])->keyBy('id');

        $ticketList = $ticketList->map(function (ScenicTicket $ticket) use ($shopList) {
            /** @var ScenicShop $shop */
            $shop = $shopList->get($ticket->shop_id);
            $ticket['shopInfo'] = $shop;

            unset($ticket->shop_id);
            unset($ticket->status);
            unset($ticket->failure_reason);
            unset($ticket->superior_promotion_commission_rate);
            unset($ticket->superior_promotion_commission_upper_limit);
            unset($ticket->created_at);
            unset($ticket->updated_at);

            return $ticket;
        });

        return $this->success($ticketList);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }

        $scenicIds = TicketScenicService::getInstance()
            ->getListByTicketId($ticket->id)->pluck('scenic_id')->toArray();
        $specList = TicketSpecService::getInstance()
            ->getSpecListByTicketId($ticket->id, ['category_id', 'price_list']);
        $ticket['scenicIds'] = $scenicIds;
        $ticket['specList'] = $specList;

        return $this->success($ticket);
    }
}
