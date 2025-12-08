<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Mall\Scenic\ScenicTicket;
use App\Services\Mall\Scenic\ScenicService;
use App\Services\Mall\Scenic\ScenicShopManagerService;
use App\Services\Mall\Scenic\ScenicShopService;
use App\Services\Mall\Scenic\ScenicTicketCategoryService;
use App\Services\Mall\Scenic\ScenicTicketService;
use App\Services\Mall\Scenic\TicketScenicService;
use App\Services\Mall\Scenic\TicketSpecService;
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
        $scenic = ScenicService::getInstance()->getScenicById($scenicId);

        $ticketIds = TicketScenicService::getInstance()
            ->getListByScenicId($scenicId)->pluck('ticket_id')->toArray();
        $ticketList = ScenicTicketService::getInstance()->getListByIds($ticketIds);

        $shopIds = $ticketList->pluck('shop_id')->toArray();
        $shopList = ScenicShopService::getInstance()
            ->getShopListByIds($shopIds, ['id', 'user_id', 'name', 'type', 'owner_avatar', 'owner_name'])
            ->keyBy('id');
        $shopManagerListGroup = ScenicShopManagerService::getInstance()
            ->getListByShopIds($shopIds, ['id', 'shop_id', 'user_id', 'avatar', 'nickname', 'role_id'])
            ->groupBy('shop_id');

        $ticketList = $ticketList->map(function (ScenicTicket $ticket) use ($scenic, $shopList, $shopManagerListGroup) {
            $ticket['scenicId'] = $scenic->id;
            $ticket['scenicCover'] = json_decode($scenic->image_list)[0];
            $ticket['scenicName'] = $scenic->name;

            $shop = $shopList->get($ticket->shop_id);
            $ticket['shopInfo'] = $shop;

            $managerList = $shopManagerListGroup->get($ticket->shop_id);
            $ticket['managerList'] = $managerList ?: [];

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
