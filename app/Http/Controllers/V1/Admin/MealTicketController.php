<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\MealTicket;
use App\Services\Mall\Catering\CateringMerchantService;
use App\Services\Mall\Catering\CateringShopService;
use App\Services\MealTicketService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\MealTicketListInput;

class MealTicketController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var MealTicketListInput $input */
        $input = MealTicketListInput::new();
        $page = MealTicketService::getInstance()->getList($input);
        $list = collect($page->items())->map(function (MealTicket $ticket) {
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
            return $this->fail(CodeResponse::NOT_FOUND, '当前代金券不存在');
        }
        $ticket['restaurantIds'] = $ticket->restaurantIds();

        $shop = CateringShopService::getInstance()->getShopById($ticket->shop_id);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮商家店铺不存在');
        }
        $merchant = CateringMerchantService::getInstance()->getMerchantById($shop->merchant_id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮商家不存在');
        }
        $ticket['shop_info'] = $shop;
        $ticket['merchant_info'] = $merchant;
        unset($shop->merchant_id);
        unset($ticket->shop_id);

        return $this->success($ticket);
    }

    public function approve()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = MealTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前代金券不存在');
        }
        $ticket->status = 1;
        $ticket->save();

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $ticket = MealTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前代金券不存在');
        }
        $ticket->status = 2;
        $ticket->failure_reason = $reason;
        $ticket->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = MealTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前代金券不存在');
        }
        $ticket->delete();

        return $this->success();
    }
}
