<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catering\MealTicket;
use App\Services\Mall\Catering\CateringMerchantService;
use App\Services\Mall\Catering\CateringShopService;
use App\Services\MealTicketService;
use App\Services\UserTaskService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\CommissionInput;
use App\Utils\Inputs\Admin\MealTicketListInput;
use Illuminate\Support\Facades\DB;

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
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐券不存在');
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

    public function editCommission()
    {
        /** @var CommissionInput $input */
        $input = CommissionInput::new();
        $id = $this->verifyRequiredId('id');

        $ticket = MealTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐券不存在');
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

        $ticket = MealTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐券不存在');
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
                ->getByMerchantId(3, $ticket->shopInfo->merchant_id, 2);
            if (!is_null($userTask) && in_array($userTask->product_id, $ticket->restaurantIds())) {
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

        $ticket = MealTicketService::getInstance()->getTicketById($id);
        if (is_null($ticket)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐券不存在');
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
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐券不存在');
        }
        $ticket->delete();

        return $this->success();
    }
}
