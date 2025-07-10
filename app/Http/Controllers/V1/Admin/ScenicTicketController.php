<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScenicTicket;
use App\Services\ScenicMerchantService;
use App\Services\ScenicShopService;
use App\Services\ScenicTicketService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\CommissionInput;
use App\Utils\Inputs\Admin\ScenicTicketListInput;

class ScenicTicketController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var ScenicTicketListInput $input */
        $input = ScenicTicketListInput::new();
        $page = ScenicTicketService::getInstance()->getList($input);
        $list = collect($page->items())->map(function (ScenicTicket $ticket) {
            $ticket['scenicIds'] = $ticket->scenicIds();
            return $ticket;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }
        $ticket['scenicIds'] = $ticket->scenicIds();

        $shop = ScenicShopService::getInstance()->getShopById($ticket->shop_id);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点商家店铺不存在');
        }
        $merchant = ScenicMerchantService::getInstance()->getMerchantById($shop->merchant_id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点服务商不存在');
        }

        $ticket['shop_info'] = $shop;
        $ticket['merchant_info'] = $merchant;
        unset($shop->merchant_id);
        unset($ticket->shop_id);

        return $this->success($ticket);
    }

    public function editCommission()
    {
        /** @var CommissionInput $input */
        $input = CommissionInput::new();
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }

        if ($input->promotionCommissionRate) {
            $ticket->promotion_commission_rate = $input->promotionCommissionRate;
        }
        if ($input->promotionCommissionUpperLimit) {
            $ticket->promotion_commission_upper_limit = $input->promotionCommissionUpperLimit;
        }
        if ($input->superiorPromotionCommissionRate) {
            $ticket->superior_promotion_commission_rate = $input->superiorPromotionCommissionRate;
        }
        if ($input->superiorPromotionCommissionUpperLimit) {
            $ticket->superior_promotion_commission_upper_limit = $input->superiorPromotionCommissionUpperLimit;
        }
        $ticket->save();

        return $this->success();
    }

    public function approve()
    {
        /** @var CommissionInput $input */
        $input = CommissionInput::new();
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }

        $ticket->status = 1;
        $ticket->promotion_commission_rate = $input->promotionCommissionRate;
        $ticket->promotion_commission_upper_limit = $input->promotionCommissionUpperLimit;
        $ticket->superior_promotion_commission_rate = $input->superiorPromotionCommissionRate;
        $ticket->superior_promotion_commission_upper_limit = $input->superiorPromotionCommissionUpperLimit;
        $ticket->save();

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $ticket = ScenicTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }
        $ticket->status = 2;
        $ticket->failure_reason = $reason;
        $ticket->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $ticket = ScenicTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }
        $ticket->delete();

        return $this->success();
    }
}
