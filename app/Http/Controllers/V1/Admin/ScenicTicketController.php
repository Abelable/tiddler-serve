<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mall\Scenic\ScenicTicket;
use App\Services\Mall\Scenic\ScenicMerchantService;
use App\Services\Mall\Scenic\ScenicShopService;
use App\Services\Mall\Scenic\ScenicTicketService;
use App\Services\Task\UserTaskService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\CommissionInput;
use App\Utils\Inputs\Admin\ScenicTicketListInput;
use Illuminate\Support\Facades\DB;

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
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点商家不存在');
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

        DB::transaction(function () use ($ticket, $input) {
            $ticket->status = 1;
            $ticket->promotion_commission_rate = $input->promotionCommissionRate;
            $ticket->promotion_commission_upper_limit = $input->promotionCommissionUpperLimit;
            $ticket->superior_promotion_commission_rate = $input->superiorPromotionCommissionRate;
            $ticket->superior_promotion_commission_upper_limit = $input->superiorPromotionCommissionUpperLimit;
            $ticket->save();

            // 邀请商家入驻活动
            $userTask = UserTaskService::getInstance()
                ->getByMerchantId(1, $ticket->shopInfo->merchant_id, 2);
            if (!is_null($userTask) && in_array($userTask->product_id, $ticket->scenicIds())) {
                $userTask->step = 3;
                $userTask->save();
            }
        });

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
