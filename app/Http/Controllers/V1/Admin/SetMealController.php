<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mall\Catering\SetMeal;
use App\Services\Mall\Catering\CateringMerchantService;
use App\Services\Mall\Catering\CateringShopService;
use App\Services\Mall\Catering\SetMealService;
use App\Services\UserTaskService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\CommissionInput;
use App\Utils\Inputs\Admin\SetMealListInput;
use Illuminate\Support\Facades\DB;

class SetMealController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var SetMealListInput $input */
        $input = SetMealListInput::new();
        $page = SetMealService::getInstance()->getList($input);
        $list = collect($page->items())->map(function (SetMeal $setMeal) {
            $setMeal['restaurantIds'] = $setMeal->restaurantIds();
            return $setMeal;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $setMeal = SetMealService::getInstance()->getSetMealById($id);
        if (is_null($setMeal)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮套餐不存在');
        }
        $setMeal['restaurantIds'] = $setMeal->restaurantIds();

        $shop = CateringShopService::getInstance()->getShopById($setMeal->shop_id);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮商家店铺不存在');
        }
        $merchant = CateringMerchantService::getInstance()->getMerchantById($shop->merchant_id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮商家不存在');
        }
        $setMeal['shop_info'] = $shop;
        $setMeal['merchant_info'] = $merchant;
        unset($shop->merchant_id);
        unset($setMeal->shop_id);

        return $this->success($setMeal);
    }

    public function editCommission()
    {
        /** @var CommissionInput $input */
        $input = CommissionInput::new();
        $id = $this->verifyRequiredId('id');

        $ticket = SetMealService::getInstance()->getSetMealById($id);
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

        $setMeal = SetMealService::getInstance()->getSetMealById($id);
        if (is_null($setMeal)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮套餐不存在');
        }

        DB::transaction(function () use ($setMeal, $input) {
            $setMeal->status = 1;
            $setMeal->promotion_commission_rate = $input->promotionCommissionRate;
            $setMeal->promotion_commission_upper_limit = $input->promotionCommissionUpperLimit;
            $setMeal->superior_promotion_commission_rate = $input->superiorPromotionCommissionRate;
            $setMeal->superior_promotion_commission_upper_limit = $input->superiorPromotionCommissionUpperLimit;
            $setMeal->save();

            // 邀请商家入驻活动
            $userTask = UserTaskService::getInstance()
                ->getByMerchantId(3, $setMeal->shopInfo->merchant_id, 2);
            if (!is_null($userTask) && in_array($userTask->product_id, $setMeal->restaurantIds())) {
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

        $setMeal = SetMealService::getInstance()->getSetMealById($id);
        if (is_null($setMeal)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮套餐不存在');
        }
        $setMeal->status = 2;
        $setMeal->failure_reason = $reason;
        $setMeal->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $setMeal = SetMealService::getInstance()->getSetMealById($id);
        if (is_null($setMeal)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前餐饮套餐不存在');
        }
        $setMeal->delete();

        return $this->success();
    }
}
